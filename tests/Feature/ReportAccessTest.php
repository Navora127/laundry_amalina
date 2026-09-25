<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_staff_can_open_reports(): void
    {
        $staff = User::factory()->cashier()->create();
        $customer = User::factory()->create();

        $this->actingAs($staff)->get(route('admin.reports.index'))->assertOk();
        $this->actingAs($customer)->get(route('admin.reports.index'))->assertForbidden();
    }

    public function test_staff_can_export_pdf(): void
    {
        $staff = User::factory()->cashier()->create();
        Order::factory()->count(2)->create();

        $this->actingAs($staff)
            ->get(route('admin.reports.pdf'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }
}
