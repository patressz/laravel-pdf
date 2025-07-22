<?php

declare(strict_types=1);

namespace Tests;

use Orchestra\Testbench\Concerns\WithWorkbench;
use Patressz\LaravelPdf\PdfServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use WithWorkbench;
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Define environment setup.
     */
    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');

        $this->ensureDirectoryExists(storage_path('framework'));
        $this->ensureDirectoryExists(storage_path('framework/views'));
        $this->ensureDirectoryExists(storage_path('framework/sessions'));
        $this->ensureDirectoryExists(storage_path('framework/cache/data'));
    }

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

    /**
     * Ensure directory exists.
     */
    protected function ensureDirectoryExists(string $path): void
    {
        if (! is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}
