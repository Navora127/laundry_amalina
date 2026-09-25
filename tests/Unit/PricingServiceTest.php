<?php

namespace Tests\Unit;

use App\Enums\ServiceUnit;
use App\Models\LaundryService;
use App\Services\PricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PricingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_kilogram_pricing_on_the_server(): void
    {
        $service = LaundryService::factory()->create([
            'unit' => ServiceUnit::Kilogram,
            'price' => 10000,
        ]);

        $result = app(PricingService::class)->estimate([
            ['service_id' => $service->id, 'qty' => 2.5],
        ]);

        $this->assertSame(25000, $result['subtotal']);
        $this->assertSame(25000, $result['total']);
    }

    public function test_piece_services_reject_fractional_quantity(): void
    {
        $service = LaundryService::factory()->create([
            'unit' => ServiceUnit::Piece,
            'price' => 15000,
        ]);

        $this->expectException(ValidationException::class);

        app(PricingService::class)->estimate([
            ['service_id' => $service->id, 'qty' => 1.5],
        ]);
    }
}
