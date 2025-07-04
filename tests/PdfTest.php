<?php

declare(strict_types=1);

use Patressz\LaravelPdf\Enums\Format;
use Patressz\LaravelPdf\PdfBuilder;

it('can instantiate the PdfBuilder', function () {
    $pdfBuilder = PdfBuilder::create();

    expect($pdfBuilder)->toBeInstanceOf(PdfBuilder::class);
});

it('can change format', function (string|Format $format) {
    $pdfBuilder = PdfBuilder::create()
        ->format($format);

    expect($pdfBuilder->format)->toBe($format);
})
    ->with([
        'A1',
        Format::A5->value,
    ]);

it('can change `nodeBinaryPath`', function () {
    $pdfBuilder = PdfBuilder::create()
        ->setNodeBinaryPath('/usr/local/bin/node');

    expect($pdfBuilder->nodeBinaryPath)->toBe('/usr/local/bin/node');
});

it('delete temporary files', function () {
    $pdfBuilder = PdfBuilder::create();

    $pdfBuilder
        ->view('layout')
        ->save(getTempDir().'/test.pdf');

    expect($pdfBuilder)
        ->tmpDirectory->exists()->toBeFalse()
        ->and(file_exists($pdfBuilder->tmpFile))->toBeFalse();
});
