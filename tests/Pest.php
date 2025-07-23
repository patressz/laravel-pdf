<?php

declare(strict_types=1);

use Tests\TestCase;

uses(TestCase::class)
    ->in(__DIR__)
    ->afterEach(function (): void {
        if (file_exists(getTempDir())) {
            array_map('unlink', glob(getTempDir('/*.*')));
            rmdir(getTempDir());
        }
    });

function getTempDir(?string $path = null): string
{
    return __DIR__.'/temp'.($path ? '/'.$path : '');
}

expect()->extend('toHaveDimensions', function (int $width, int $height): void {
    $imagick = new Imagick;
    $imagick->setResolution(96, 96);
    $imagick->readImage($this->value);

    $dimensions = $imagick->getImageGeometry();

    $imagick->clear();
    $imagick->destroy();

    expect($dimensions['width'])->toBeWithinRange($width - 2, $width + 2);
    expect($dimensions['height'])->toBeWithinRange($height - 2, $height + 2);
});

expect()->extend('toBeWithinRange', function (int $min, int $max) {
    return $this->toBeGreaterThanOrEqual($min)
        ->toBeLessThanOrEqual($max);
});