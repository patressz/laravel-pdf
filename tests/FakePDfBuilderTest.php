<?php

declare(strict_types=1);

use Illuminate\View\View;
use Patressz\LaravelPdf\Enums\Format;
use Patressz\LaravelPdf\Enums\Unit;
use Patressz\LaravelPdf\Facades\Pdf;

beforeEach(function () {
    Pdf::fake();
});

describe('FakePDfBuilderTest', function () {
    it('can assert view is set correctly', function () {
        Pdf::view('layout');

        Pdf::assertView('layout');
    });

    it('can assert url is set correctly', function () {
        $url = 'https://example.com';
        Pdf::fromUrl($url);

        Pdf::assertUrl($url);
    });

    it('can throws an exception when url is invalid', function () {
        Pdf::fromUrl('example.com');
    })->throws(InvalidArgumentException::class, 'Expected a valid URL format starts with http:// or https://');

    it('can assert view with callback is set correctly', function () {
        Pdf::view('layout', [
            'key' => 'value',
        ]);

        Pdf::assertView('layout', function (View $view, array $data) {
            return $view->name() === 'layout' && $data['key'] === 'value';
        });
    });

    it('can assert html is set correctly', function () {
        $htmlContent = '<html><body>Test</body></html>';
        Pdf::html($htmlContent);

        Pdf::assertHtml($htmlContent);
    });

    it('can assert that PDF header template is set correctly', function () {
        $headerContent = '<html><body>Header</body></html>';
        Pdf::headerTemplate($headerContent);

        Pdf::assertHeaderTemplate($headerContent);
    });

    it('can assert that PDF footer template is set correctly', function () {
        $footerContent = '<html><body>Footer</body></html>';
        Pdf::footerTemplate($footerContent);

        Pdf::assertFooterTemplate($footerContent);
    });

    it('can assert that PDF format is set correctly', function () {
        Pdf::format(Format::A6);

        Pdf::assertFormat('A6');
    });

    it('can assert that PDF width is set correctly', function () {
        $width = 210;
        Pdf::width($width);

        Pdf::assertWidth($width);
    });

    it('can assert that PDF width is set correctly using inches', function () {
        $width = 210;
        Pdf::width($width, Unit::Inch);

        Pdf::assertWidth($width, Unit::Inch);
    });

    it('can assert that PDF height is set correctly', function () {
        $height = 297;
        Pdf::height($height);

        Pdf::assertHeight($height);
    });

    it('can assert that PDF height is set correctly using inches', function () {
        $height = 297;
        Pdf::height($height, Unit::Inch);

        Pdf::assertHeight($height, Unit::Inch);
    });

    it('can assert that PDF landscape mode is set to true', function () {
        Pdf::landscape();

        Pdf::assertLandscape();
    });

    it('can assert that PDF outline is set correctly', function () {
        Pdf::outline();

        Pdf::assertOutline();
    });

    it('can assert that PDF prefers CSS page size', function () {
        Pdf::preferCSSPageSize();

        Pdf::assertPreferCSSPageSize();
    });

    it('can assert that PDF margin is set correctly', function () {
        Pdf::margins(
            top: 10,
            unit: Unit::Inch
        );

        Pdf::assertMargins(
            expectedTop: 10,
            expectedRight: 0,
            expectedBottom: 0,
            expectedLeft: 0,
            unit: Unit::Inch
        );
    });

    it('can assert that PDF print background is enabled', function () {
        Pdf::printBackground();

        Pdf::assertPrintBackground();
    });

    it('can assert that PDF displays header and footer', function () {
        Pdf::displayHeaderFooter();

        Pdf::assertDisplayHeaderFooter();
    });

    it('can assert that PDF scale is set correctly', function () {
        $scale = 1.5;
        Pdf::scale($scale);

        Pdf::assertScale($scale);
    });

    it('can assert that PDF is tagged', function () {
        Pdf::tagged();

        Pdf::assertTagged();
    });

    it('can assert that PDF page ranges are set correctly', function () {
        $pageRanges = '1-5, 8, 11-13';
        Pdf::pageRanges($pageRanges);

        Pdf::assertPageRanges($pageRanges);
    });

    it('can assert that PDF name is set correctly', function () {
        $name = 'my-document.pdf';
        Pdf::name($name);

        Pdf::assertName($name);
    });

    it('can assert that PDF was saved to a specific path', function () {
        $path = 'fake/path/to/document.pdf';
        Pdf::save($path);

        Pdf::assertSaved($path);
    });

    it('can assert that PDF was downloaded', function () {
        Pdf::download();

        Pdf::assertDownloaded();
    });

    it('can assert that PDF was downloaded to a specific path', function () {
        $downloadFileName = 'downloaded.pdf';
        Pdf::download($downloadFileName);

        Pdf::assertDownloaded($downloadFileName);
    });

    it('can assert that PDF was displayed inline', function () {
        $downloadFileName = 'inline.pdf';
        Pdf::inline($downloadFileName);

        Pdf::assertInline($downloadFileName);
    });
});
