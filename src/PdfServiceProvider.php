<?php

declare(strict_types=1);

namespace Patressz\LaravelPdf;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

final class PdfServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        File::ensureDirectoryExists(storage_path('framework/views'));
        
        $this->registerBladeDirectives();
    }

    /**
     * Register custom Blade directives for PDF generation.
     */
    private function registerBladeDirectives(): void
    {
        Blade::directive('pageNumber', function () {
            return "<?php echo '<span class=\"pageNumber\"></span>'; ?>";
        });

        Blade::directive('totalPages', function () {
            return "<?php echo '<span class=\"totalPages\"></span>'; ?>";
        });

        Blade::directive('pageBreak', function () {
            return "<?php echo '<div style=\"page-break-after: always;\"></div>'; ?>";
        });
    }
}
