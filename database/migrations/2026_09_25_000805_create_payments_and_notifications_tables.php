<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('method', 20);
            $table->string('status', 20)->default('pending');
            $table->unsignedBigInteger('amount');
            $table->string('reference_number', 100)->nullable();
            $table->string('proof_disk', 50)->nullable();
            $table->string('proof_path', 500)->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'created_at']);
            $table->index(['status', 'paid_at']);
        });

        Schema::create('wa_templates', function (Blueprint $table) {
            $table->id();
            $table->string('status', 40)->unique();
            $table->string('name', 100);
            $table->text('body');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('wa_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('channel', 30)->default('wa_manual');
            $table->string('phone_e164', 20);
            $table->text('message');
            $table->string('delivery_status', 20)->default('prepared');
            $table->timestamp('prepared_at');
            $table->timestamp('opened_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'prepared_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wa_logs');
        Schema::dropIfExists('wa_templates');
        Schema::dropIfExists('payments');
    }
};
