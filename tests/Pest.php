<?php

declare(strict_types=1);

use Tests\TestCase;

uses(TestCase::class)
    ->in(__DIR__)
    ->afterEach(function (): void {
        if (file_exists(getTempDir())) {
            array_map('unlink', glob(getTempDir().'/*.*'));
            rmdir(getTempDir());
        }
    });


function getTempDir(): string
{
    return __DIR__.'/temp';
}