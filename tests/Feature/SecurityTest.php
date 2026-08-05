<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityTest extends TestCase
{
    public function test_sensitive_routes_require_authentication(): void
    {
        foreach ([
            ['get', '/users'],
            ['get', '/orders'],
            ['get', '/add_food'],
            ['post', '/upload_food'],
            ['delete', '/delete_food/1'],
            ['delete', '/remove_cart/1'],
            ['post', '/confirm_order'],
        ] as [$method, $uri]) {
            $this->{$method}($uri)->assertRedirect('/login');
        }
    }

    public function test_browser_security_headers_are_applied(): void
    {
        $response = $this->get('/login');

        $response->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        $this->assertStringContainsString(
            "default-src 'self'",
            (string) $response->headers->get('Content-Security-Policy')
        );
    }
}
