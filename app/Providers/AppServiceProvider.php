<?php

namespace App\Providers;

use App\Services\Ai\AiGateway;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Satu gerbang AI untuk seluruh permintaan: pelacakan provider penjawab
        // (penanda 🔵/🟠 di balasan bot) disimpan di instance ini, jadi kalau
        // tiap kelas dapat instance sendiri, penandanya ikut hilang.
        $this->app->singleton(AiGateway::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
