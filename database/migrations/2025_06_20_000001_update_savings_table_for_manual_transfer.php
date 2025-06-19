<?php
// database/migrations/2025_06_20_000001_update_savings_table_for_manual_transfer.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('savings', function (Blueprint $table) {
            // Add new columns for manual transfer tracking
            $table->enum('status', ['PENDING', 'TRANSFERRED'])->default('PENDING')->after('amount');
            $table->date('transfer_date')->nullable()->after('transaction_date');
            $table->string('transfer_method')->nullable()->after('transfer_date'); // BCA/Octo Pay/etc
            $table->string('transfer_reference')->nullable()->after('transfer_method'); // Nomor referensi

            // Remove old columns that are not needed anymore
            $table->dropColumn(['bank_balance', 'is_verified']);

            // Add indexes for better performance
            $table->index('status');
            $table->index('transfer_date');
        });
    }

    public function down()
    {
        Schema::table('savings', function (Blueprint $table) {
            // Reverse the changes
            $table->dropIndex(['status']);
            $table->dropIndex(['transfer_date']);

            $table->dropColumn([
                'status',
                'transfer_date',
                'transfer_method',
                'transfer_reference'
            ]);

            // Add back old columns
            $table->decimal('bank_balance', 15, 2)->default(0);
            $table->boolean('is_verified')->default(false);
        });
    }
};
