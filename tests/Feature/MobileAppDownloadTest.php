<?php

namespace Tests\Feature;

use App\Models\User;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tests\TestCase;

class MobileAppDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_find_the_app_download_in_the_homepage_navigation(): void
    {
        $response = $this->get('/')->assertOk();

        $this->assertHomepageDownloadLink($response->getContent());
    }

    public function test_customers_can_find_the_app_download_in_the_homepage_navigation(): void
    {
        $customer = User::factory()->create(['usertype' => 'user']);

        $response = $this->actingAs($customer)->get('/')->assertOk();

        $this->assertHomepageDownloadLink($response->getContent());
    }

    public function test_guests_can_download_the_published_android_apk(): void
    {
        $response = $this->get(route('mobile-app.download'));

        $this->assertGuest();
        $this->assertApkDownload($response);
    }

    public function test_customers_can_download_the_published_android_apk(): void
    {
        $customer = User::factory()->create(['usertype' => 'user']);

        $response = $this->actingAs($customer)->get(route('mobile-app.download'));

        $this->assertAuthenticatedAs($customer);
        $this->assertApkDownload($response);
    }

    public function test_head_requests_identify_the_download_as_an_android_apk(): void
    {
        $response = $this->head(route('mobile-app.download'));

        $this->assertApkDownload($response);
        $response->assertHeader('Content-Length', (string) filesize(public_path('downloads/Mi-Cusina.apk')));
    }

    private function assertApkDownload(TestResponse $response): void
    {
        $response->assertOk()
            ->assertDownload('Mi-Cusina.apk')
            ->assertHeader('Content-Type', 'application/vnd.android.package-archive');

        $this->assertTrue($response->headers->hasCacheControlDirective('private'));
        $this->assertTrue($response->headers->hasCacheControlDirective('no-store'));
        $this->assertSame('0', $response->headers->getCacheControlDirective('max-age'));
        $this->assertInstanceOf(BinaryFileResponse::class, $response->baseResponse);
        $this->assertSame(
            realpath(public_path('downloads/Mi-Cusina.apk')),
            $response->baseResponse->getFile()->getRealPath()
        );
    }

    private function assertHomepageDownloadLink(string $html): void
    {
        $document = new DOMDocument;
        $document->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);

        $links = (new DOMXPath($document))->query(
            '//nav[@aria-label="Primary"]//a[@href="'.route('mobile-app.download').'"]'
        );

        $this->assertCount(1, $links);
        $this->assertSame('Download App', trim($links->item(0)->textContent));
    }
}
