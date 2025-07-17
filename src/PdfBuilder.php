<?php

declare(strict_types=1);

namespace Patressz\LaravelPdf;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Patressz\LaravelPdf\Enums\Format;
use Patressz\LaravelPdf\Enums\Unit;
use RuntimeException;
use Spatie\TemporaryDirectory\TemporaryDirectory;

final class PdfBuilder implements Responsable
{
    private const string BINARY_PATH = __DIR__.'/../bin/playwright.cjs';

    /**
     * The path to the Node.js binary.
     */
    public ?string $nodeBinaryPath = null;

    /**
     * Temporary directory for storing the HTML file.
     */
    public ?TemporaryDirectory $tmpDirectory = null;

    /**
     * Temporary file for storing the HTML content.
     */
    public ?string $tmpFile = null;

    /**
     * The options for the PDF generation.
     *
     * @var array<string, mixed>
     */
    public array $options = [];

    /**
     * The margins for the PDF document.
     *
     * @var array<string, float>
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
     * The html content to convert to PDF.
     */
    private ?string $html = null;

    /**
     * Create a new instance of the PdfBuilder.
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
        $html = view($view, $data)->render();

        $this->html = $html;

        return $this;
    }

    /**
     * Set the paper format of the PDF document.
     *
     * If set, this takes priority over
     * [`width`](https://playwright.dev/docs/api/class-page#page-pdf-option-width) or
     * [`height`](https://playwright.dev/docs/api/class-page#page-pdf-option-height) options. Defaults to 'Letter'.
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
     *
     * Paper orientation. Defaults to `false`.
     */
    public function landscape(): self
    {
        $this->options['landscape'] = true;

        return $this;
    }

    /**
     * Whether or not to embed the document outline into the PDF. Defaults to `false`.
     */
    public function outline(): self
    {
        $this->options['outline'] = true;

        return $this;
    }

    /**
     * Prefer CSS page size over the format option.
     *
     * Give any CSS `@page` size declared in the page priority over what is declared in
     * [`width`](https://playwright.dev/docs/api/class-page#page-pdf-option-width) and
     * [`height`](https://playwright.dev/docs/api/class-page#page-pdf-option-height) or
     * [`format`](https://playwright.dev/docs/api/class-page#page-pdf-option-format) options. Defaults to `false`, which
     * will scale the content to fit the paper size.
     */
    public function preferCSSPageSize(): self
    {
        $this->options['preferCSSPageSize'] = true;

        return $this;
    }

    /**
     * Set the margins for the PDF document.
     *
     * Paper margins, defaults to none.
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
     * Print background graphics. Defaults to `false`.
     */
    public function printBackground(): self
    {
        $this->options['printBackground'] = true;

        return $this;
    }

    /**
     * Display header and footer. Defaults to `false`.
     */
    public function displayHeaderFooter(): self
    {
        $this->options['displayHeaderFooter'] = true;

        return $this;
    }

    /**
     * Set the scale factor for the PDF rendering.
     *
     * Scale of the webpage rendering. Defaults to `1`. Scale amount must be between `0.1` and `2`.
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
     * Whether or not to generate tagged (accessible) PDF. Defaults to `false`.
     */
    public function tagged(): self
    {
        $this->options['tagged'] = true;

        return $this;
    }

    /**
     * Paper ranges to print, e.g., '1-5, 8, 11-13'. Defaults to the empty string, which means print all pages.
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
     * Set the path to the Node.js binary.
     */
    public function setNodeBinaryPath(string $path): self
    {
        $this->nodeBinaryPath = $path;

        return $this;
    }

    /**
     * Save the generated PDF to the specified output path.
     *
     * @return string The path to the saved PDF file.
     */
    public function save(string $outputPath): string
    {
        $pdfContent = $this->raw();

        $directory = dirname($outputPath);

        if (! is_dir($directory)) {
            if (! mkdir($directory, 0755, true)) {
                throw new RuntimeException(sprintf('Failed to create directory [%s]', $directory));
            }
        }

        if (! is_writable($directory)) {
            throw new RuntimeException(sprintf('Directory [%s] is not writable', $directory));
        }

        $bytesWritten = file_put_contents($outputPath, $pdfContent);

        if ($bytesWritten === false) {
            throw new RuntimeException(sprintf('Failed to write PDF content to [%s]', $outputPath));
        }

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
     * Generate the PDF and return its content as a base64-encoded string.
     */
    public function base64(): string
    {
        return $this->callBinary();
    }

    /**
     * Generate the PDF and return its content as a raw binary string.
     */
    public function raw(): string
    {
        $decodedContent = base64_decode($this->callBinary(), true);

        if ($decodedContent === false) {
            throw new RuntimeException('Failed to decode base64 content.');
        }

        return $decodedContent;
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
     * Generate the PDF and return it as a response.
     */
    public function toResponse($request): Response
    {
        if (! array_key_exists('Content-Disposition', $this->responseHeaders)) {
            $this->responseHeaders['Content-Disposition'] = 'inline; filename="document.pdf"';
        }

        $pdfContent = $this->raw();

        return response($pdfContent, 200, $this->responseHeaders);
    }

    /**
     * Generate the PDF and return its content as a base64-encoded string.
     */
    private function callBinary(): string
    {
        $this->createTemporaryFile();

        try {
            $process = Process::timeout(60)
                ->env([
                    'PATH' => PHP_OS_FAMILY === 'Windows' ? getenv('PATH') : 'PATH:/usr/local/bin:/opt/homebrew/bin',
                    'NODE_PATH' => base_path().'/node_modules',
                ])
                ->run([
                    $this->getNodeBinaryPath(),
                    self::BINARY_PATH,
                    "--filePath={$this->tmpFile}",
                    '--margins='.json_encode($this->margins),
                    '--options='.json_encode($this->options),
                ]);

            if ($process->failed()) {
                throw new RuntimeException('Failed to generate PDF: '.$process->errorOutput());
            }

            $base64 = $process->output();

            if (blank($base64)) {
                throw new RuntimeException('PDF generation failed: No output received.');
            }

            return $base64;
        } finally {
            $this->cleanupTemporaryFiles();
        }
    }

    /**
     * Get the path to the Node.js binary.
     */
    private function getNodeBinaryPath(): string
    {
        if ($this->nodeBinaryPath) {
            return $this->nodeBinaryPath;
        }

        $possiblePaths = PHP_OS_FAMILY === 'Windows'
        ? [
            getenv('ProgramFiles').DIRECTORY_SEPARATOR.'nodejs'.DIRECTORY_SEPARATOR.'node.exe',
            getenv('ProgramFiles(x86)').DIRECTORY_SEPARATOR.'nodejs'.DIRECTORY_SEPARATOR.'node.exe',
        ]
        : [
            '/usr/local/bin/node',
            '/opt/homebrew/bin/node',
            '/usr/bin/node',
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        $whichCommand = PHP_OS_FAMILY === 'Windows' ? ['where', 'node'] : ['which', 'node'];
        $process = Process::run($whichCommand);

        if ($process->successful()) {
            return mb_trim($process->output());
        }

        throw new RuntimeException('Node.js binary not found. Please set the path using setNodeBinaryPath() method.');
    }

    /**
     * Create temporary file to store the HTML content.
     */
    private function createTemporaryFile(): void
    {
        $this->tmpDirectory = TemporaryDirectory::make()->name('laravel-pdf-'.uniqid());

        $this->tmpFile = $this->tmpDirectory->path('document.html');

        file_put_contents($this->tmpFile, $this->html);
    }

    /**
     * Cleanup temporary files after PDF generation.
     */
    private function cleanupTemporaryFiles(): void
    {
        if ($this->tmpDirectory) {
            $this->tmpDirectory->delete();
        }
    }
}
