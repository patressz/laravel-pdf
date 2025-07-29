<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Patressz\LaravelPdf\Enums\Format;
use Patressz\LaravelPdf\Enums\Unit;
use Patressz\LaravelPdf\Facades\Pdf;
use Patressz\LaravelPdf\PdfBuilder;

it('can instantiate the PdfBuilder', function () {
    $pdfBuilder = PdfBuilder::create();

    expect($pdfBuilder)->toBeInstanceOf(PdfBuilder::class);
});

it('can change format', function (string|Format $format) {
    $pdfBuilder = Pdf::format($format);

    expect($pdfBuilder->options['format'])->toBe($format);
})
    ->with([
        'A1',
        Format::A5->value,
    ]);

it('throws exception for invalid format', function () {
    Pdf::format('INVALID');
})->throws(InvalidArgumentException::class);

it('can format with enum', function () {
    $pdfBuilder = Pdf::format(Format::A6);

    expect($pdfBuilder->options['format'])->toBe('A6');
});

it('can change orientation to landscape', function () {
    $pdfBuilder = Pdf::landscape();

    expect($pdfBuilder->options['landscape'])->toBeTrue();
});

it('can change width and height', function () {
    $pdfBuilder = Pdf::width(210)
        ->height(297);

    expect($pdfBuilder->options['width'])->toBe('210mm');
    expect($pdfBuilder->options['height'])->toBe('297mm');
});

it('can change width and height in inches', function () {
    $pdfBuilder = Pdf::width(8.27, Unit::Inch)
        ->height(11.69, Unit::Inch);

    expect($pdfBuilder->options['width'])->toBe('8.27in');
    expect($pdfBuilder->options['height'])->toBe('11.69in');
});

it('throws exception for invalid unit in width', function () {
    Pdf::width(210, 'invalid');
})->throws(InvalidArgumentException::class);

it('throws exception for invalid unit in height', function () {
    Pdf::height(297, 'invalid');
})->throws(InvalidArgumentException::class);

it('can set outline', function () {
    $pdfBuilder = Pdf::outline();

    expect($pdfBuilder->options['outline'])->toBeTrue();
});

it('can set scale', function () {
    $pdfBuilder = Pdf::scale(1.5);

    expect($pdfBuilder->options['scale'])->toBe(1.5);
});

it('throws exception for invalid scale', function (float $scale) {
    Pdf::scale($scale);
})
    ->with([0, 2.5])
    ->throws(InvalidArgumentException::class, 'Scale must be a positive number between 0.1 and 2.');

it('can set print background', function () {
    $pdfBuilder = Pdf::printBackground();

    expect($pdfBuilder->options['printBackground'])->toBeTrue();
});

it('can set tagged PDF', function () {
    $pdfBuilder = Pdf::tagged();

    expect($pdfBuilder->options['tagged'])->toBeTrue();
});

it('can set page ranges', function () {
    $pdfBuilder = Pdf::pageRanges('1-5, 8, 11-13');

    expect($pdfBuilder->options['pageRanges'])->toBe('1-5, 8, 11-13');
});

it('throws exception for empty page ranges', function () {
    Pdf::pageRanges('');
})->throws(InvalidArgumentException::class, 'Page ranges cannot be empty.');

it('can set prefer CSS page size', function () {
    $pdfBuilder = Pdf::preferCSSPageSize();

    expect($pdfBuilder->options['preferCSSPageSize'])->toBeTrue();
});

it('can set display header footer', function () {
    $pdfBuilder = Pdf::displayHeaderFooter();

    expect($pdfBuilder->options['displayHeaderFooter'])->toBeTrue();
});

it('can set header template', function () {
    $pdfBuilder = Pdf::headerTemplate('<div>Header</div>');

    expect($pdfBuilder->headerHtml)->toBe('<div>Header</div>');
});

it('can set footer template', function () {
    $pdfBuilder = Pdf::footerTemplate('<div>Footer</div>');

    expect($pdfBuilder->footerHtml)->toBe('<div>Footer</div>');
});

it('can set header and footer templates with view', function () {
    $pdfBuilder = Pdf::headerTemplate(view('header'))
        ->footerTemplate(view('footer'));

    expect($pdfBuilder->headerHtml)->toBe(view('header')->render());
    expect($pdfBuilder->footerHtml)->toBe(view('footer')->render());
});

it('can set margin', function () {
    $pdfBuilder = Pdf::margins(10, 20, 30, 40);

    expect($pdfBuilder->margins['top'])->toBe('10mm');
    expect($pdfBuilder->margins['right'])->toBe('20mm');
    expect($pdfBuilder->margins['bottom'])->toBe('30mm');
    expect($pdfBuilder->margins['left'])->toBe('40mm');
});

it('can set margin in inches', function () {
    $pdfBuilder = Pdf::margins(1, 2, 3, 4, Unit::Inch);

    expect($pdfBuilder->margins['top'])->toBe('1in');
    expect($pdfBuilder->margins['right'])->toBe('2in');
    expect($pdfBuilder->margins['bottom'])->toBe('3in');
    expect($pdfBuilder->margins['left'])->toBe('4in');
});

it('can set download filename', function () {
    $pdfBuilder = Pdf::name('invoice.pdf');

    expect($pdfBuilder->downloadFileName)->toBe('invoice.pdf');
});

it('can set download filename with `name()` method over `download()` method `downloadFileName` argument', function () {
    $pdfBuilder = Pdf::name('invoice-1.pdf')
        ->download('invoice.pdf');

    expect($pdfBuilder->downloadFileName)->toBe('invoice-1.pdf');
});

it('can change `nodeBinaryPath`', function () {
    $pdfBuilder = Pdf::setNodeBinaryPath('/usr/local/bin/node');

    expect($pdfBuilder->nodeBinaryPath)->toBe('/usr/local/bin/node');
});

it('automatically adds pdf extension', function () {
    $pdfBuilder = Pdf::name('invoice');

    expect($pdfBuilder->downloadFileName)->toBe('invoice.pdf');
});

it('can add custom headers', function () {
    $pdfBuilder = Pdf::addHeaders([
        'X-Custom-Header' => 'test-value',
        'Cache-Control' => 'no-cache',
    ]);

    expect($pdfBuilder->responseHeaders['X-Custom-Header'])->toBe('test-value');
    expect($pdfBuilder->responseHeaders['Cache-Control'])->toBe('no-cache');
});

it('can set download headers', function () {
    $pdfBuilder = Pdf::download('test.pdf');

    expect($pdfBuilder->responseHeaders['Content-Type'])->toBe('application/pdf');
    expect($pdfBuilder->responseHeaders['Content-Disposition'])->toBe('attachment; filename="test.pdf"');
});

it('can set inline headers', function () {
    $pdfBuilder = Pdf::inline('test.pdf');

    expect($pdfBuilder->responseHeaders['Content-Type'])->toBe('application/pdf');
    expect($pdfBuilder->responseHeaders['Content-Disposition'])->toBe('inline; filename="test.pdf"');
});

it('can set view', function () {
    $pdfBuilder = Pdf::view('layout');

    $html = view('layout')->render();

    expect($pdfBuilder->html)->toBe($html);
});

it('can set html content', function () {
    $pdfBuilder = Pdf::html('<h1>Hello World</h1>');

    expect($pdfBuilder->html)->toBe('<h1>Hello World</h1>');
});

it('can set html content using `html()` method over `view()` method', function () {
    $pdfBuilder = Pdf::view('layout')
        ->html('<h1>Hello World</h1>');

    expect($pdfBuilder->html)->toBe('<h1>Hello World</h1>');
});

it('delete temporary files', function () {
    $pdfBuilder = PdfBuilder::create();

    $pdfBuilder
        ->view('layout')
        ->headerTemplate(view('header'))
        ->footerTemplate(view('footer'))
        ->save(getTempDir('test.pdf'));

    expect($pdfBuilder)
        ->tmpDirectory->exists()->toBeFalse()
        ->and(file_exists($pdfBuilder->tmpFiles['document']))->toBeFalse()
        ->and(file_exists($pdfBuilder->tmpFiles['header']))->toBeFalse()
        ->and(file_exists($pdfBuilder->tmpFiles['footer']))->toBeFalse();
});

it('can save PDF file with correct format', function () {
    $path = Pdf::view('layout')
        ->format(Format::A2)
        ->save(getTempDir('test.pdf'));

    expect($path)
        ->toBeFile()
        ->toBeReadableFile()
        ->toHaveDimensions(1587, 2246);
});

it('can save PDF file with correct format using `width()` and `height()` method', function () {
    $path = Pdf::view('layout')
        ->width(1587, Unit::Pixel)
        ->height(2246, Unit::Pixel)
        ->save(getTempDir('test.pdf'));

    expect($path)
        ->toBeFile()
        ->toBeReadableFile()
        ->toHaveDimensions(1587, 2246);
});

it('can save PDF file with landscape orientation', function () {
    $path = Pdf::view('layout')
        ->landscape()
        ->save(getTempDir('test.pdf'));

    expect($path)
        ->toBeFile()
        ->toBeReadableFile()
        ->toHaveDimensions(1054, 816);
});

it('can save PDF file generated from URL', function () {
    Route::get('/invoice', function () {
        return view('layout');
    })->name('invoice');

    $url = route('invoice');

    $path = Pdf::fromUrl($url)
        ->save(getTempDir('test.pdf'));

    expect($path)
        ->toBeFile()
        ->toBeReadableFile();
});
