<?php

declare(strict_types=1);

namespace Tests;

use Patressz\Skeleton\ExampleServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
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
    }

    /**
     * Get package providers.
     *
     * @return array<int, class-string<ServiceProvider>>
     */
    protected function getPackageProviders($app): array
    {
        return [
            ExampleServiceProvider::class,
        ];
    }
}
