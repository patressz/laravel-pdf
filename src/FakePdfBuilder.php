<?php

declare(strict_types=1);

namespace Patressz\LaravelPdf;

use Closure;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\View\View;
use InvalidArgumentException;
use Patressz\LaravelPdf\Enums\Format;
use Patressz\LaravelPdf\Enums\Unit;
use PHPUnit\Framework\Assert as PHPUnit;

final class FakePdfBuilder implements Responsable
{
    use Conditionable;

    /**
     * The options for the PDF generation.
     *
     * @var array<string, mixed>
     */
    public array $options = [];

    /**
     * The margins for the PDF document.
     *
     * @var array<string, string>
     */
    public array $margins = [];

    /**
     * The headers to be included in the response.
     *
     * @var array<string, string>
     */
    public array $responseHeaders = [];

    /**
     * The filename to be used when downloading the PDF.
     */
    public ?string $downloadFileName = null;

    /**
     * The view name to render the HTML content.
     */
    public ?View $view = null;

    /**
     * The HTML content to convert to PDF.
     */
    public ?string $html = null;

    /**
     * The header HTML for the PDF.
     */
    public ?string $headerHtml = null;

    /**
     * The footer HTML for the PDF.
     */
    public ?string $footerHtml = null;

    /**
     * Store generated PDFs for testing assertions.
     *
     * @var array<string, string>
     */
    public array $generatedPdfs = [];

    /**
     * Create a new instance of the FakePdfBuilder.
     */
    public static function create(): self
    {
        return new self;
    }

    /**
     * Pass the view name and data to render the HTML content.
     *
     * @param  array<mixed, mixed>  $data
     */
    public function view(string $view, array $data = []): self
    {
        if ($this->html === null) {
            $view = view($view, $data);

            $this->view = $view;
            $this->html = $view->render();
        }

        return $this;
    }

    /**
     * Set the HTML content to convert to PDF.
     */
    public function html(string $html): self
    {
        $this->html = $html;

        return $this;
    }

    /**
     * Set the HTML content for the header template.
     */
    public function headerTemplate(string|View $template): self
    {
        $this->displayHeaderFooter();

        if ($template instanceof View) {
            $template = $template->render();
        }

        $this->headerHtml = $template;

        return $this;
    }

    /**
     * Set the HTML content for the footer template.
     */
    public function footerTemplate(string|View $template): self
    {
        $this->displayHeaderFooter();

        if ($template instanceof View) {
            $template = $template->render();
        }

        $this->footerHtml = $template;

        return $this;
    }

    /**
     * Set the paper format of the PDF document.
     */
    public function format(string|Format $format): self
    {
        if ($format instanceof Format) {
            $this->options['format'] = mb_strtoupper($format->value);

            return $this;
        }

        $format = mb_strtoupper($format);

        $validFormats = collect(Format::cases())->map(fn (Format $case) => mb_strtoupper($case->value));

        if (! in_array($format, $validFormats->toArray(), true)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid format [%s]. Expected one of: [%s]',
                $format,
                $validFormats->implode(', '),
            ));
        }

        $this->options['format'] = $format;

        return $this;
    }

    /**
     * Set the width of the PDF document.
     */
    public function width(float $width, Unit|string $unit = Unit::Millimeter): self
    {
        if ($unit instanceof Unit) {
            $unit = $unit->value;
        }

        $unit = mb_strtolower($unit);

        $validUnits = collect(Unit::cases())->map(fn (Unit $case) => mb_strtolower($case->value));

        if (! in_array($unit, $validUnits->toArray(), true)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid unit [%s]. Expected one of: [%s]',
                $unit,
                $validUnits->implode(', ')
            ));
        }

        $this->options['width'] = "{$width}{$unit}";

        return $this;
    }

    /**
     * Set the height of the PDF document.
     */
    public function height(float $height, Unit|string $unit = Unit::Millimeter): self
    {
        if ($unit instanceof Unit) {
            $unit = $unit->value;
        }

        $unit = mb_strtolower($unit);

        $validUnits = collect(Unit::cases())->map(fn (Unit $case) => mb_strtolower($case->value));

        if (! in_array($unit, $validUnits->toArray(), true)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid unit [%s]. Expected one of: [%s]',
                $unit,
                $validUnits->implode(', ')
            ));
        }

        $this->options['height'] = "{$height}{$unit}";

        return $this;
    }

    /**
     * Set the PDF orientation to landscape.
     */
    public function landscape(): self
    {
        $this->options['landscape'] = true;

        return $this;
    }

    /**
     * Whether or not to embed the document outline into the PDF.
     */
    public function outline(): self
    {
        $this->options['outline'] = true;

        return $this;
    }

    /**
     * Prefer CSS page size over the format option.
     */
    public function preferCSSPageSize(): self
    {
        $this->options['preferCSSPageSize'] = true;

        return $this;
    }

    /**
     * Set the margins for the PDF document.
     */
    public function margins(
        float $top = 0,
        float $right = 0,
        float $bottom = 0,
        float $left = 0,
        Unit|string $unit = Unit::Millimeter,
    ): self {
        if ($unit instanceof Unit) {
            $unit = $unit->value;
        }

        $unit = mb_strtolower($unit);

        $validUnits = collect(Unit::cases())->map(fn (Unit $case) => mb_strtolower($case->value));

        if (! in_array($unit, $validUnits->toArray(), true)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid unit [%s]. Expected one of: [%s]',
                $unit,
                $validUnits->implode(', ')
            ));
        }

        $this->margins = [
            'top' => "{$top}{$unit}",
            'right' => "{$right}{$unit}",
            'bottom' => "{$bottom}{$unit}",
            'left' => "{$left}{$unit}",
        ];

        return $this;
    }

    /**
     * Print background graphics.
     */
    public function printBackground(): self
    {
        $this->options['printBackground'] = true;

        return $this;
    }

    /**
     * Display header and footer.
     */
    public function displayHeaderFooter(): self
    {
        $this->options['displayHeaderFooter'] = true;

        return $this;
    }

    /**
     * Set the scale factor for the PDF rendering.
     */
    public function scale(float $scale): self
    {
        if ($scale < 0.1 || $scale > 2) {
            throw new InvalidArgumentException('Scale must be a positive number between 0.1 and 2.');
        }

        $this->options['scale'] = $scale;

        return $this;
    }

    /**
     * Whether or not to generate tagged (accessible) PDF.
     */
    public function tagged(): self
    {
        $this->options['tagged'] = true;

        return $this;
    }

    /**
     * Paper ranges to print.
     */
    public function pageRanges(string $ranges): self
    {
        if (empty($ranges)) {
            throw new InvalidArgumentException('Page ranges cannot be empty.');
        }

        $this->options['pageRanges'] = $ranges;

        return $this;
    }

    /**
     * Set the filename to be used when downloading the PDF.
     */
    public function name(string $downloadFileName): self
    {
        $this->downloadFileName = Str::of($downloadFileName)
            ->lower()
            ->pipe(fn (string $name): string => Str::endsWith($name, 'pdf') ? $name : $name.'.pdf')
            ->toString();

        return $this;
    }

    /**
     * Set the path to the Node.js binary (fake implementation).
     */
    public function setNodeBinaryPath(string $path): self
    {
        return $this;
    }

    /**
     * Save the generated PDF to the specified output path (fake implementation).
     */
    public function save(string $outputPath): string
    {
        $this->generatedPdfs[$outputPath] = $this->generateFakePdfContent();

        return $outputPath;
    }

    /**
     * Set the response headers for downloading the PDF.
     */
    public function download(string $downloadFileName = 'document.pdf'): self
    {
        if (! $this->downloadFileName) {
            $this->name($downloadFileName);
        }

        $this->addHeaders([
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$this->downloadFileName.'"',
        ]);

        return $this;
    }

    /**
     * Set the response headers for inline PDF.
     */
    public function inline(string $downloadFileName = 'document.pdf'): self
    {
        if (! $this->downloadFileName) {
            $this->name($downloadFileName);
        }

        $this->addHeaders([
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$this->downloadFileName.'"',
        ]);

        return $this;
    }

    /**
     * Generate the PDF and return its content as a base64-encoded string (fake implementation).
     */
    public function base64(): string
    {
        return base64_encode($this->generateFakePdfContent());
    }

    /**
     * Generate the PDF and return its content as a raw binary string (fake implementation).
     */
    public function raw(): string
    {
        return $this->generateFakePdfContent();
    }

    /**
     * Add custom headers to the response.
     *
     * @param  array<string, string>  $headers
     */
    public function addHeaders(array $headers): self
    {
        foreach ($headers as $key => $value) {
            $this->responseHeaders[$key] = $value;
        }

        return $this;
    }

    /**
     * Generate the PDF and return it as a response (fake implementation).
     */
    public function toResponse($request): Response
    {
        return new Response;
    }

    /**
     * Generate fake PDF content for testing.
     */
    private function generateFakePdfContent(): string
    {
        return "%PDF-1.4\n1 0 obj\n<<\n/Type /Catalog\n/Pages 2 0 R\n>>\nendobj\n2 0 obj\n<<\n/Type /Pages\n/Kids [3 0 R]\n/Count 1\n>>\nendobj\n3 0 obj\n<<\n/Type /Page\n/Parent 2 0 R\n/MediaBox [0 0 612 792]\n>>\nendobj\nxref\n0 4\n0000000000 65535 f \n0000000010 00000 n \n0000000053 00000 n \n0000000100 00000 n \ntrailer\n<<\n/Size 4\n/Root 1 0 R\n>>\nstartxref\n149\n%%EOF\n";
    }

    /**
     * Assert that the view matches the expected value.
     *
     * @param  (Closure(\Illuminate\View\View, array): bool)|null  $callback  Optional callback to further validate the view data.
     */
    public function assertView(string $view, ?Closure $callback = null): self
    {
        PHPUnit::assertEquals($view, $this->view->name(), 'View does not match expected value.');

        if ($callback instanceof Closure) {
            $result = $callback($this->view, $this->view->getData());

            if (is_bool($result)) {
                PHPUnit::assertTrue($result, 'View does not match expected value.');
            }
        }

        return $this;
    }

    /**
     * Assert that the HTML content matches the expected value.
     */
    public function assertHtml(string $expectedHtml): self
    {
        PHPUnit::assertEquals($expectedHtml, $this->html, 'HTML content does not match expected value.');

        return $this;
    }

    /**
     * Assert that the header template matches the expected HTML.
     */
    public function assertHeaderTemplate(string $expectedHeaderHtml): self
    {
        PHPUnit::assertEquals($expectedHeaderHtml, $this->headerHtml, 'Header template does not match expected value.');

        return $this;
    }

    /**
     * Assert that the footer template matches the expected HTML.
     */
    public function assertFooterTemplate(string $expectedFooterHtml): self
    {
        PHPUnit::assertEquals($expectedFooterHtml, $this->footerHtml, 'Footer template does not match expected value.');

        return $this;
    }

    /**
     * Assert that the format matches the expected value.
     */
    public function assertFormat(string|Format $expectedFormat): self
    {
        if ($expectedFormat instanceof Format) {
            $expectedFormat = mb_strtoupper($expectedFormat->value);
        } else {
            $expectedFormat = mb_strtoupper($expectedFormat);
        }

        $validFormats = collect(Format::cases())->map(fn (Format $case) => mb_strtoupper($case->value));

        if (! in_array($expectedFormat, $validFormats->toArray(), true)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid format [%s]. Expected one of: [%s]',
                $expectedFormat,
                $validFormats->implode(', '),
            ));
        }

        PHPUnit::assertEquals($expectedFormat, $this->options['format'], 'Format does not match expected value.');

        return $this;
    }

    /**
     * Assert that the width matches the expected value.
     */
    public function assertWidth(float $expectedWidth, Unit|string $unit = Unit::Millimeter): self
    {
        if ($unit instanceof Unit) {
            $unit = $unit->value;
        }

        $unit = mb_strtolower($unit);

        $validUnits = collect(Unit::cases())->map(fn (Unit $case) => mb_strtolower($case->value));

        if (! in_array($unit, $validUnits->toArray(), true)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid unit [%s]. Expected one of: [%s]',
                $unit,
                $validUnits->implode(', ')
            ));
        }

        $expectedWidth = "{$expectedWidth}{$unit}";

        PHPUnit::assertEquals($expectedWidth, $this->options['width'], 'Width does not match expected value.');

        return $this;
    }

    /**
     * Assert that the height matches the expected value.
     */
    public function assertHeight(float $expectedHeight, Unit|string $unit = Unit::Millimeter): self
    {
        if ($unit instanceof Unit) {
            $unit = $unit->value;
        }

        $unit = mb_strtolower($unit);

        $validUnits = collect(Unit::cases())->map(fn (Unit $case) => mb_strtolower($case->value));

        if (! in_array($unit, $validUnits->toArray(), true)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid unit [%s]. Expected one of: [%s]',
                $unit,
                $validUnits->implode(', ')
            ));
        }

        $expectedHeight = "{$expectedHeight}{$unit}";

        PHPUnit::assertEquals($expectedHeight, $this->options['height'], 'Height does not match expected value.');

        return $this;
    }

    /**
     * Assert that the landscape option matches the expected value.
     */
    public function assertLandscape(bool $expectedLandscape): self
    {
        PHPUnit::assertEquals($expectedLandscape, $this->options['landscape'], 'Landscape option does not match expected value.');

        return $this;
    }

    /**
     * Assert that the outline option matches the expected value.
     */
    public function assertOutline(bool $expectedOutline): self
    {
        PHPUnit::assertEquals($expectedOutline, $this->options['outline'], 'Outline option does not match expected value.');

        return $this;
    }

    /**
     * Assert that the preferCSSPageSize option matches the expected value.
     */
    public function assertPreferCSSPageSize(bool $expectedPreferCSSPageSize): self
    {
        PHPUnit::assertEquals($expectedPreferCSSPageSize, $this->options['preferCSSPageSize'], 'Prefer CSS page size option does not match expected value.');

        return $this;
    }

    /**
     * Assert that the margins match the expected values.
     */
    public function assertMargins(
        float $expectedTop = 0,
        float $expectedRight = 0,
        float $expectedBottom = 0,
        float $expectedLeft = 0,
        Unit|string $unit = Unit::Millimeter
    ): self {
        if ($unit instanceof Unit) {
            $unit = $unit->value;
        }

        $unit = mb_strtolower($unit);

        $validUnits = collect(Unit::cases())->map(fn (Unit $case) => mb_strtolower($case->value));

        if (! in_array($unit, $validUnits->toArray(), true)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid unit [%s]. Expected one of: [%s]',
                $unit,
                $validUnits->implode(', ')
            ));
        }

        $expectedMargins = [
            'top' => "{$expectedTop}{$unit}",
            'right' => "{$expectedRight}{$unit}",
            'bottom' => "{$expectedBottom}{$unit}",
            'left' => "{$expectedLeft}{$unit}",
        ];

        PHPUnit::assertEquals($expectedMargins, $this->margins, 'Margins do not match expected values.');

        return $this;
    }

    /**
     * Assert that the printBackground option matches the expected value.
     */
    public function assertPrintBackground(bool $expectedPrintBackground): self
    {
        PHPUnit::assertEquals($expectedPrintBackground, $this->options['printBackground'], 'Print background option does not match expected value.');

        return $this;
    }

    /**
     * Assert that the displayHeaderFooter option matches the expected value.
     */
    public function assertDisplayHeaderFooter(bool $expectedDisplayHeaderFooter): self
    {
        PHPUnit::assertEquals($expectedDisplayHeaderFooter, $this->options['displayHeaderFooter'], 'Display header footer option does not match expected value.');

        return $this;
    }

    /**
     * Assert that the scale option matches the expected value.
     */
    public function assertScale(float $expectedScale): self
    {
        PHPUnit::assertEquals($expectedScale, $this->options['scale'], 'Scale option does not match expected value.');

        return $this;
    }

    /**
     * Assert that the tagged option matches the expected value.
     */
    public function assertTagged(bool $expectedTagged): self
    {
        PHPUnit::assertEquals($expectedTagged, $this->options['tagged'], 'Tagged option does not match expected value.');

        return $this;
    }

    /**
     * Assert that the pageRanges option matches the expected value.
     */
    public function assertPageRanges(string $expectedPageRanges): self
    {
        PHPUnit::assertEquals($expectedPageRanges, $this->options['pageRanges'], 'Page ranges option does not match expected value.');

        return $this;
    }

    /**
     * Assert that the name option matches the expected value.
     */
    public function assertName(string $expectedName): self
    {
        PHPUnit::assertEquals($expectedName, $this->downloadFileName, 'Name option does not match expected value.');

        return $this;
    }

    /**
     * Assert that a PDF was saved to the specified path.
     */
    public function assertSaved(string $path): self
    {
        PHPUnit::assertArrayHasKey($path, $this->generatedPdfs, "PDF was not saved to path: {$path}");

        return $this;
    }

    /**
     * Assert that a PDF was downloaded with the specified filename.
     */
    public function assertDownloaded(string $downloadFileName = 'document.pdf'): self
    {
        PHPUnit::assertEquals($this->downloadFileName, $downloadFileName, 'Download filename does not match expected value.');
        PHPUnit::assertArrayHasKey('Content-Type', $this->responseHeaders, 'Content-Type header is not set.');
        PHPUnit::assertArrayHasKey('Content-Disposition', $this->responseHeaders, 'Content-Disposition header is not set.');

        PHPUnit::assertEquals(
            'application/pdf',
            $this->responseHeaders['Content-Type'],
            'Content-Type header does not match expected value.'
        );

        PHPUnit::assertEquals(
            'attachment; filename="'.$this->downloadFileName.'"',
            $this->responseHeaders['Content-Disposition'],
            'Content-Disposition header does not match expected value.'
        );

        return $this;
    }

    /**
     * Assert that a PDF was displayed inline with the specified filename.
     */
    public function assertInline(string $downloadFileName = 'document.pdf'): self
    {
        PHPUnit::assertEquals($this->downloadFileName, $downloadFileName, 'Inline filename does not match expected value.');
        PHPUnit::assertArrayHasKey('Content-Type', $this->responseHeaders, 'Content-Type header is not set.');
        PHPUnit::assertArrayHasKey('Content-Disposition', $this->responseHeaders, 'Content-Disposition header is not set.');

        PHPUnit::assertEquals(
            'application/pdf',
            $this->responseHeaders['Content-Type'],
            'Content-Type header does not match expected value.'
        );

        PHPUnit::assertEquals(
            'inline; filename="'.$this->downloadFileName.'"',
            $this->responseHeaders['Content-Disposition'],
            'Content-Disposition header does not match expected value.'
        );

        return $this;
    }
}
