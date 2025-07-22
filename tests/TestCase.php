<?php

declare(strict_types=1);

namespace Tests;

use Orchestra\Testbench\Concerns\WithWorkbench;
use Patressz\LaravelPdf\PdfServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use WithWorkbench;

    /**
     * Get package providers.
     *
     * @return array<int, class-string<ServiceProvider>>
     */
    protected function getPackageProviders($app): array
    {
        return [
            PdfServiceProvider::class,
        ];
    }
}
