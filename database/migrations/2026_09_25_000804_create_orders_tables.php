<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source', 20)->default('online');
            $table->string('fulfillment_method', 20);
            $table->string('status', 40)->default('awaiting_confirmation');
            $table->string('payment_status', 20)->default('unpaid');
            $table->string('currency', 3)->default('IDR');

            $table->string('customer_name', 255);
            $table->string('customer_phone_e164', 20);
            $table->string('customer_email')->nullable();

            $table->unsignedBigInteger('estimated_subtotal')->default(0);
            $table->unsignedBigInteger('final_subtotal')->default(0);
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->unsignedBigInteger('delivery_fee')->default(0);
            $table->unsignedBigInteger('estimated_total')->default(0);
            $table->unsignedBigInteger('final_total')->default(0);

            $table->foreignId('pickup_address_id')->nullable()->constrained('addresses')->nullOnDelete();
            $table->foreignId('delivery_address_id')->nullable()->constrained('addresses')->nullOnDelete();
            $table->timestamp('scheduled_at')->nullable();
            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('cancellation_reason', 500)->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('price_reviewed_at')->nullable();
            $table->timestamp('price_approved_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->string('idempotency_key', 100)->nullable()->unique();
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();

            $table->index(['status', 'created_at'], 'orders_status_created_idx');
            $table->index(['customer_id', 'created_at'], 'orders_customer_created_idx');
            $table->index(['fulfillment_method', 'created_at'], 'orders_fulfillment_created_idx');
            $table->index(['payment_status', 'created_at'], 'orders_payment_created_idx');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->restrictOnDelete();
            $table->string('service_name', 150);
            $table->string('service_unit', 10);
            $table->decimal('estimated_qty', 12, 2);
            $table->decimal('actual_qty', 12, 2)->nullable();
            $table->unsignedBigInteger('unit_price');
            $table->unsignedBigInteger('estimated_subtotal');
            $table->unsignedBigInteger('final_subtotal');
            $table->timestamps();

            $table->index(['order_id', 'service_id']);
        });

        Schema::create('order_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('from_status', 40)->nullable();
            $table->string('to_status', 40);
            $table->string('note', 500)->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['order_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_logs');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
