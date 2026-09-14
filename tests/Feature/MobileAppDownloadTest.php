<?php

namespace Tests\Feature;

use App\Models\User;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $response = $this->get(route('mobile-app.download'))
            ->assertOk()
            ->assertDownload('Mi-Cusina.apk');

        $this->assertGuest();
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
