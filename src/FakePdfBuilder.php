<?php

declare(strict_types=1);

namespace Patressz\LaravelPdf;

use Closure;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use InvalidArgumentException;
use LogicException;
use Patressz\LaravelPdf\Enums\Format;
use Patressz\LaravelPdf\Enums\Unit;
use PHPUnit\Framework\Assert as PHPUnit;

class FakePdfBuilder extends PdfBuilder implements Responsable
{
    /**
     * The view name to render the HTML content.
     */
    public ?View $view = null;

    /**
     * Store generated PDFs for testing assertions.
     *
     * @var array<string, string>
     */
    public array $generatedPdfs = [];

    /**
     * Pass the view name and data to render the HTML content.
     *
     * Note: The `html()` method always takes precedence over this method, regardless of call order.
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
     * Save the generated PDF to the specified output path (fake implementation).
     */
    public function save(string $outputPath): string
    {
        $this->generatedPdfs[$outputPath] = $this->generateFakePdfContent();

        return $outputPath;
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
     * Assert that the PDF was generated from a URL.
     */
    public function assertUrl(string $url): self
    {
        PHPUnit::assertTrue($this->isFromUrl, 'PDF was not generated from a URL.');
        PHPUnit::assertEquals($url, $this->url, 'URL does not match expected value.');

        return $this;
    }

    /**
     * Assert that the view matches the expected value.
     *
     * @param  (Closure(View, array<mixed, mixed>): mixed)|null  $callback  Optional callback to further validate the view data.
     */
    public function assertView(string $view, ?Closure $callback = null): self
    {
        if ($this->view === null) {
            throw new LogicException('No view has been set for assertion.');
        }

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
    public function assertLandscape(): self
    {
        PHPUnit::assertEquals(true, $this->options['landscape'], 'Landscape option does not match expected value.');

        return $this;
    }

    /**
     * Assert that the outline option matches the expected value.
     */
    public function assertOutline(): self
    {
        PHPUnit::assertEquals(true, $this->options['outline'], 'Outline option does not match expected value.');

        return $this;
    }

    /**
     * Assert that the preferCSSPageSize option matches the expected value.
     */
    public function assertPreferCSSPageSize(): self
    {
        PHPUnit::assertEquals(true, $this->options['preferCSSPageSize'], 'Prefer CSS page size option does not match expected value.');

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
    public function assertPrintBackground(): self
    {
        PHPUnit::assertEquals(true, $this->options['printBackground'], 'Print background option does not match expected value.');

        return $this;
    }

    /**
     * Assert that the displayHeaderFooter option matches the expected value.
     */
    public function assertDisplayHeaderFooter(): self
    {
        PHPUnit::assertEquals(true, $this->options['displayHeaderFooter'], 'Display header footer option does not match expected value.');

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
    public function assertTagged(): self
    {
        PHPUnit::assertEquals(true, $this->options['tagged'], 'Tagged option does not match expected value.');

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
