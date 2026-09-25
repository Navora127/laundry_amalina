<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_is_available(): void
    {
        $this->get('/')->assertOk()->assertSee('LaundryKu');
    }

    public function test_catalog_page_is_available(): void
    {
        $this->get('/katalog')->assertOk()->assertSee('Katalog layanan');
    }
}
