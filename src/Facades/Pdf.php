<?php

declare(strict_types=1);

namespace Patressz\LaravelPdf\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Patressz\LaravelPdf\PdfBuilder create()
 * @method static \Patressz\LaravelPdf\PdfBuilder view(string $view, array<mixed, mixed> $data = [])
 * @method static \Patressz\LaravelPdf\PdfBuilder html(string $html)
 * @method static \Patressz\LaravelPdf\PdfBuilder headerTemplate(\Illuminate\View\View|string $template)
 * @method static \Patressz\LaravelPdf\PdfBuilder footerTemplate(\Illuminate\View\View|string $template)
 * @method static \Patressz\LaravelPdf\PdfBuilder format(\Patressz\LaravelPdf\Enums\Format|string $format)
 * @method static \Patressz\LaravelPdf\PdfBuilder width(float $width, \Patressz\LaravelPdf\Enums\Unit|string $unit = 'mm')
 * @method static \Patressz\LaravelPdf\PdfBuilder height(float $height, \Patressz\LaravelPdf\Enums\Unit|string $unit = 'mm')
 * @method static \Patressz\LaravelPdf\PdfBuilder landscape()
 * @method static \Patressz\LaravelPdf\PdfBuilder outline()
 * @method static \Patressz\LaravelPdf\PdfBuilder preferCSSPageSize()
 * @method static \Patressz\LaravelPdf\PdfBuilder margins(float $top = 0, float $right = 0, float $bottom = 0, float $left = 0, \Patressz\LaravelPdf\Enums\Unit|string $unit = 'mm')
 * @method static \Patressz\LaravelPdf\PdfBuilder printBackground()
 * @method static \Patressz\LaravelPdf\PdfBuilder displayHeaderFooter()
 * @method static \Patressz\LaravelPdf\PdfBuilder scale(float $scale)
 * @method static \Patressz\LaravelPdf\PdfBuilder tagged()
 * @method static \Patressz\LaravelPdf\PdfBuilder pageRanges(string $ranges)
 * @method static \Patressz\LaravelPdf\PdfBuilder name(string $downloadFileName)
 * @method static \Patressz\LaravelPdf\PdfBuilder setNodeBinaryPath(string $path)
 * @method static string save(string $outputPath)
 * @method static \Patressz\LaravelPdf\PdfBuilder download(string $downloadFileName = 'document.pdf')
 * @method static \Patressz\LaravelPdf\PdfBuilder inline(string $downloadFileName = 'document.pdf')
 * @method static string base64()
 * @method static string raw()
 * @method static \Patressz\LaravelPdf\PdfBuilder addHeaders(array<string, string> $headers)
 * @method static \Illuminate\Http\Response toResponse(\Illuminate\Http\Request $request)
 * @method static \Patressz\LaravelPdf\PdfBuilder|mixed when(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \Patressz\LaravelPdf\PdfBuilder|mixed unless(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \Patressz\LaravelPdf\FakePdfBuilder assertView(string $view, \Closure|null $callback = null)
 * @method static \Patressz\LaravelPdf\FakePdfBuilder assertHtml(string $expectedHtml)
 * @method static \Patressz\LaravelPdf\FakePdfBuilder assertHeaderTemplate(string $expectedHeaderHtml)
 * @method static \Patressz\LaravelPdf\FakePdfBuilder assertFooterTemplate(string $expectedFooterHtml)
 * @method static \Patressz\LaravelPdf\FakePdfBuilder assertFormat(\Patressz\LaravelPdf\Enums\Format|string $expectedFormat)
 * @method static \Patressz\LaravelPdf\FakePdfBuilder assertWidth(float $expectedWidth, \Patressz\LaravelPdf\Enums\Unit|string $unit = 'mm')
 * @method static \Patressz\LaravelPdf\FakePdfBuilder assertHeight(float $expectedHeight, \Patressz\LaravelPdf\Enums\Unit|string $unit = 'mm')
 * @method static \Patressz\LaravelPdf\FakePdfBuilder assertLandscape()
 * @method static \Patressz\LaravelPdf\FakePdfBuilder assertOutline()
 * @method static \Patressz\LaravelPdf\FakePdfBuilder assertPreferCSSPageSize()
 * @method static \Patressz\LaravelPdf\FakePdfBuilder assertMargins(float $expectedTop = 0, float $expectedRight = 0, float $expectedBottom = 0, float $expectedLeft = 0, \Patressz\LaravelPdf\Enums\Unit|string $unit = 'mm')
 * @method static \Patressz\LaravelPdf\FakePdfBuilder assertPrintBackground()
 * @method static \Patressz\LaravelPdf\FakePdfBuilder assertDisplayHeaderFooter()
 * @method static \Patressz\LaravelPdf\FakePdfBuilder assertScale(float $expectedScale)
 * @method static \Patressz\LaravelPdf\FakePdfBuilder assertTagged()
 * @method static \Patressz\LaravelPdf\FakePdfBuilder assertPageRanges(string $expectedPageRanges)
 * @method static \Patressz\LaravelPdf\FakePdfBuilder assertName(string $expectedName)
 * @method static \Patressz\LaravelPdf\FakePdfBuilder assertSaved(string $path)
 * @method static \Patressz\LaravelPdf\FakePdfBuilder assertDownloaded(string $downloadFileName = 'document.pdf')
 * @method static \Patressz\LaravelPdf\FakePdfBuilder assertInline(string $downloadFileName = 'document.pdf')
 *
 * @see \Patressz\LaravelPdf\PdfBuilder
 * @see \Patressz\LaravelPdf\FakePdfBuilder
 */
final class Pdf extends Facade
{
    /**
     * Indicates if the resolved instance should be cached.
     *
     * @var bool
     */
    protected static $cached = false;

    /**
     * Hotswap the underlying instance behind the facade.
     */
    public static function fake(): void
    {
        $fake = new \Patressz\LaravelPdf\FakePdfBuilder;

        self::swap($fake);
    }

    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return \Patressz\LaravelPdf\PdfBuilder::class;
    }
}
