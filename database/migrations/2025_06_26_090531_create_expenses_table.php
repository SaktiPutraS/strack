<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->date('expense_date');
            $table->decimal('amount', 15, 2);
            $table->enum('category', [
                'OPERASIONAL',
                'MARKETING',
                'PENGEMBANGAN',
                'GAJI_FREELANCE',
                'ENTERTAINMENT',
                'LAIN_LAIN'
            ]);
            $table->string('subcategory')->nullable(); // hosting, software, iklan, dll
            $table->text('description');
            $table->timestamps();

            $table->index('expense_date');
            $table->index('category');
        });
    }

    public function down()
    {
        Schema::dropIfExists('expenses');
    }
};
