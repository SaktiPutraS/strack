<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Modul Catatan Hutang Piutang.
     * Catatan: DB lokal/hosting diisi dari dump produksi; skema di-apply lewat delta SQL
     * (database/sql/2026_07_24_debt_records.sql). File migrasi ini untuk catatan repo.
     */
    public function up(): void
    {
        Schema::create('debt_records', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['HUTANG', 'PIUTANG']);
            $table->string('party_name');
            $table->string('title')->nullable();
            $table->decimal('principal_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->enum('status', ['ONGOING', 'PAID'])->default('ONGOING');
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index('status');
        });

        Schema::create('debt_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debt_record_id')->constrained('debt_records')->cascadeOnDelete();
            $table->decimal('amount', 15, 2)->default(0);
            $table->date('payment_date');
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debt_payments');
        Schema::dropIfExists('debt_records');
    }
};
