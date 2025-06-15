<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Custom Blade directive untuk format currency
        Blade::directive('currency', function ($expression) {
            return "<?php echo 'Rp ' . number_format($expression ?? 0, 0, ',', '.'); ?>";
        });

        // Custom Blade directive untuk format date
        Blade::directive('dateFormat', function ($expression) {
            return "<?php echo ($expression) ? $expression->format('d M Y') : '-'; ?>";
        });

        // Custom Blade directive untuk format percentage
        Blade::directive('percentage', function ($expression) {
            return "<?php echo number_format($expression ?? 0, 1) . '%'; ?>";
        });
    }
}
