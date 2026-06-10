<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->enum('payment_status', ['UNPAID', 'PARTIAL', 'PAID'])
                ->default('UNPAID')
                ->after('status');
        });

        // Set nilai awal sesuai data pembayaran yang sudah ada
        DB::statement("UPDATE projects SET payment_status = 'PAID'    WHERE paid_amount >= total_value AND total_value > 0");
        DB::statement("UPDATE projects SET payment_status = 'PARTIAL' WHERE paid_amount > 0 AND paid_amount < total_value");
        DB::statement("UPDATE projects SET payment_status = 'UNPAID'  WHERE paid_amount <= 0");
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('payment_status');
        });
    }
};
