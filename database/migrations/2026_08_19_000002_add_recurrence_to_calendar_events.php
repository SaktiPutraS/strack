<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calendar_events', function (Blueprint $table) {
            // NULL = sekali jalan. Isi: DAILY, WEEKDAY, WEEKLY, MONTHLY, YEARLY.
            $table->string('repeat_type', 20)->nullable()->after('completed_at');
            // Tiap berapa hari/minggu/bulan/tahun (sesuai repeat_type). Minimal 1.
            $table->unsignedSmallInteger('repeat_interval')->default(1)->after('repeat_type');
            // Khusus WEEKLY: hari yang dipilih, CSV angka 0-6 (0 = Minggu).
            $table->string('repeat_days', 20)->nullable()->after('repeat_interval');
            // Khusus MONTHLY: tanggal ke berapa. -1 berarti hari terakhir bulan itu.
            $table->smallInteger('repeat_day_of_month')->nullable()->after('repeat_days');
            // Batas akhir pengulangan (inklusif). NULL = tanpa batas.
            $table->date('repeat_until')->nullable()->after('repeat_day_of_month');
        });

        // Centang selesai per tanggal kemunculan, supaya todo harian yang sudah
        // dikerjakan hari ini tetap muncul lagi besok sebagai belum selesai.
        Schema::create('calendar_event_completions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->date('occurrence_date');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'occurrence_date'], 'cec_event_date_unique');
            $table->foreign('event_id')->references('id')->on('calendar_events')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_event_completions');

        Schema::table('calendar_events', function (Blueprint $table) {
            $table->dropColumn([
                'repeat_type',
                'repeat_interval',
                'repeat_days',
                'repeat_day_of_month',
                'repeat_until',
            ]);
        });
    }
};
