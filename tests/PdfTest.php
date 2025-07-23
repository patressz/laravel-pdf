<?php

declare(strict_types=1);

use Patressz\LaravelPdf\Enums\Format;
use Patressz\LaravelPdf\Enums\Unit;
use Patressz\LaravelPdf\PdfBuilder;

it('can instantiate the PdfBuilder', function () {
    $pdfBuilder = PdfBuilder::create();

    expect($pdfBuilder)->toBeInstanceOf(PdfBuilder::class);
});

it('can change format', function (string|Format $format) {
    $pdfBuilder = PdfBuilder::create()
        ->format($format);

    expect($pdfBuilder->options['format'])->toBe($format);
})
    ->with([
        'A1',
        Format::A5->value,
    ]);

it('throws exception for invalid format', function () {
    PdfBuilder::create()->format('INVALID');
})->throws(InvalidArgumentException::class);

it('can format with enum', function () {
    $pdfBuilder = PdfBuilder::create()
        ->format(Format::A6);

    expect($pdfBuilder->options['format'])->toBe('A6');
});

it('can change orientation to landscape', function () {
    $pdfBuilder = PdfBuilder::create()
        ->landscape();

    expect($pdfBuilder->options['landscape'])->toBeTrue();
});

it('can change width and height', function () {
    $pdfBuilder = PdfBuilder::create()
        ->width(210)
        ->height(297);

    expect($pdfBuilder->options['width'])->toBe('210mm');
    expect($pdfBuilder->options['height'])->toBe('297mm');
});

it('can change width and height in inches', function () {
    $pdfBuilder = PdfBuilder::create()
        ->width(8.27, Unit::Inch)
        ->height(11.69, Unit::Inch);

    expect($pdfBuilder->options['width'])->toBe('8.27in');
    expect($pdfBuilder->options['height'])->toBe('11.69in');
});

it('throws exception for invalid unit in width', function () {
    PdfBuilder::create()->width(210, 'invalid');
})->throws(InvalidArgumentException::class);

it('throws exception for invalid unit in height', function () {
    PdfBuilder::create()->height(297, 'invalid');
})->throws(InvalidArgumentException::class);

it('can set outline', function () {
    $pdfBuilder = PdfBuilder::create()
        ->outline();

    expect($pdfBuilder->options['outline'])->toBeTrue();
});

it('can set scale', function () {
    $pdfBuilder = PdfBuilder::create()
        ->scale(1.5);

    expect($pdfBuilder->options['scale'])->toBe(1.5);
});

it('throws exception for invalid scale', function (float $scale) {
    PdfBuilder::create()->scale($scale);
})
    ->with([0, 2.5])
    ->throws(InvalidArgumentException::class, 'Scale must be a positive number between 0.1 and 2.');

it('can set print background', function () {
    $pdfBuilder = PdfBuilder::create()
        ->printBackground();

    expect($pdfBuilder->options['printBackground'])->toBeTrue();
});

it('can set tagged PDF', function () {
    $pdfBuilder = PdfBuilder::create()
        ->tagged();

    expect($pdfBuilder->options['tagged'])->toBeTrue();
});

it('can set page ranges', function () {
    $pdfBuilder = PdfBuilder::create()
        ->pageRanges('1-5, 8, 11-13');

    expect($pdfBuilder->options['pageRanges'])->toBe('1-5, 8, 11-13');
});

it('throws exception for empty page ranges', function () {
    PdfBuilder::create()->pageRanges('');
})->throws(InvalidArgumentException::class, 'Page ranges cannot be empty.');

it('can set prefer CSS page size', function () {
    $pdfBuilder = PdfBuilder::create()
        ->preferCSSPageSize();

    expect($pdfBuilder->options['preferCSSPageSize'])->toBeTrue();
});

it('can set display header footer', function () {
    $pdfBuilder = PdfBuilder::create()
        ->displayHeaderFooter();

    expect($pdfBuilder->options['displayHeaderFooter'])->toBeTrue();
});

it('can set header template', function () {
    $pdfBuilder = PdfBuilder::create()
        ->headerTemplate('<div>Header</div>');

    expect($pdfBuilder->headerHtml)->toBe('<div>Header</div>');
});

it('can set footer template', function () {
    $pdfBuilder = PdfBuilder::create()
        ->footerTemplate('<div>Footer</div>');

    expect($pdfBuilder->footerHtml)->toBe('<div>Footer</div>');
});

it('can set header and footer templates with view', function () {
    $pdfBuilder = PdfBuilder::create()
        ->headerTemplate(view('header'))
        ->footerTemplate(view('footer'));

    expect($pdfBuilder->headerHtml)->toBe(view('header')->render());
    expect($pdfBuilder->footerHtml)->toBe(view('footer')->render());
});

it('can set margin', function () {
    $pdfBuilder = PdfBuilder::create()
        ->margins(10, 20, 30, 40);

    expect($pdfBuilder->margins['top'])->toBe('10mm');
    expect($pdfBuilder->margins['right'])->toBe('20mm');
    expect($pdfBuilder->margins['bottom'])->toBe('30mm');
    expect($pdfBuilder->margins['left'])->toBe('40mm');
});

it('can set margin in inches', function () {
    $pdfBuilder = PdfBuilder::create()
        ->margins(1, 2, 3, 4, Unit::Inch);

    expect($pdfBuilder->margins['top'])->toBe('1in');
    expect($pdfBuilder->margins['right'])->toBe('2in');
    expect($pdfBuilder->margins['bottom'])->toBe('3in');
    expect($pdfBuilder->margins['left'])->toBe('4in');
});

it('can set download filename', function () {
    $pdfBuilder = PdfBuilder::create()
        ->name('invoice.pdf');

    expect($pdfBuilder->downloadFileName)->toBe('invoice.pdf');
});

it('can set download filename with `name()` method over `download()` method `downloadFileName` argument', function () {
    $pdfBuilder = PdfBuilder::create()
        ->name('invoice-1.pdf')
        ->download('invoice.pdf');

    expect($pdfBuilder->downloadFileName)->toBe('invoice-1.pdf');
});

it('can change `nodeBinaryPath`', function () {
    $pdfBuilder = PdfBuilder::create()
        ->setNodeBinaryPath('/usr/local/bin/node');

    expect($pdfBuilder->nodeBinaryPath)->toBe('/usr/local/bin/node');
});

it('automatically adds pdf extension', function () {
    $pdfBuilder = PdfBuilder::create()
        ->name('invoice');

    expect($pdfBuilder->downloadFileName)->toBe('invoice.pdf');
});

it('can add custom headers', function () {
    $pdfBuilder = PdfBuilder::create()
        ->addHeaders([
            'X-Custom-Header' => 'test-value',
            'Cache-Control' => 'no-cache',
        ]);

    expect($pdfBuilder->responseHeaders['X-Custom-Header'])->toBe('test-value');
    expect($pdfBuilder->responseHeaders['Cache-Control'])->toBe('no-cache');
});

it('can set download headers', function () {
    $pdfBuilder = PdfBuilder::create()
        ->download('test.pdf');

    expect($pdfBuilder->responseHeaders['Content-Type'])->toBe('application/pdf');
    expect($pdfBuilder->responseHeaders['Content-Disposition'])->toBe('attachment; filename="test.pdf"');
});

it('can set inline headers', function () {
    $pdfBuilder = PdfBuilder::create()
        ->inline('test.pdf');

    expect($pdfBuilder->responseHeaders['Content-Type'])->toBe('application/pdf');
    expect($pdfBuilder->responseHeaders['Content-Disposition'])->toBe('inline; filename="test.pdf"');
});

it('can set view', function () {
    $pdfBuilder = PdfBuilder::create()
        ->view('layout');

    $html = view('layout')->render();

    expect($pdfBuilder->html)->toBe($html);
});

it('can set html content', function () {
    $pdfBuilder = PdfBuilder::create()
        ->html('<h1>Hello World</h1>');

    expect($pdfBuilder->html)->toBe('<h1>Hello World</h1>');
});

it('can set html content using `html()` method over `view()` method', function () {
    $pdfBuilder = PdfBuilder::create()
        ->view('layout')
        ->html('<h1>Hello World</h1>');

    expect($pdfBuilder->html)->toBe('<h1>Hello World</h1>');
});

it('delete temporary files', function () {
    $pdfBuilder = PdfBuilder::create();

    $pdfBuilder
        ->view('layout')
        ->headerTemplate(view('header'))
        ->footerTemplate(view('footer'))
        ->save(getTempDir().'/test.pdf');

    expect($pdfBuilder)
        ->tmpDirectory->exists()->toBeFalse()
        ->and(file_exists($pdfBuilder->tmpFiles['document']))->toBeFalse()
        ->and(file_exists($pdfBuilder->tmpFiles['header']))->toBeFalse()
        ->and(file_exists($pdfBuilder->tmpFiles['footer']))->toBeFalse();
});
