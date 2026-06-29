<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_redirects_to_dashboard(): void
    {
        // La route / est protégée — un invité est redirigé vers /login.
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }
}
