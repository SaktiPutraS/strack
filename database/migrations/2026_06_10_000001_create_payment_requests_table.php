<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('order_id')->unique();          // dikirim ke Midtrans sebagai order_id
            $table->string('gateway')->default('midtrans');
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['PENDING', 'PAID', 'EXPIRED', 'FAILED', 'CANCELLED'])->default('PENDING');
            $table->string('payment_url')->nullable();      // Snap redirect_url / link bayar
            $table->string('snap_token')->nullable();
            $table->string('gateway_ref')->nullable();      // transaction_id dari Midtrans
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->json('raw_response')->nullable();        // audit: response & callback terakhir
            $table->timestamps();

            $table->index('status');
            $table->index(['project_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_requests');
    }
};
