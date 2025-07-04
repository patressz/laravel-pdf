<?php

declare(strict_types=1);

namespace Patressz\LaravelPdf;

use Illuminate\Support\Facades\Process;
use InvalidArgumentException;
use Patressz\LaravelPdf\Enums\Format;
use RuntimeException;
use Spatie\TemporaryDirectory\TemporaryDirectory;

final class PdfBuilder
{
    private const string BINARY_PATH = __DIR__.'/../bin/playwright.cjs';

    /**
     * The format of the PDF document.
     */
    public string $format = 'A4';

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
     */
    public function view(string $view, array $data = []): self
    {
        $html = view($view, $data)->render();

        $this->html = $html;

        return $this;
    }

    /**
     * Set the format of the PDF document.
     */
    public function format(string|Format $format): self
    {
        if ($format instanceof Format) {
            $this->format = $format->value;

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

        $this->format = $format;

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
        $this->createTemporaryFile();

        $process = Process::forever()
            ->env([
                'PATH' => PHP_OS_FAMILY === 'Windows' ? getenv('PATH') : 'PATH:/usr/local/bin:/opt/homebrew/bin',
                'NODE_PATH' => base_path().'/node_modules',
            ])
            ->run([
                $this->getNodeBinaryPath(),
                self::BINARY_PATH,
                "--format={$this->format}",
                "--filePath={$this->tmpFile}",
                "--outputPath={$outputPath}",
            ]);

        if ($process->failed()) {
            throw new RuntimeException('Failed to generate PDF: '.$process->errorOutput());
        }

        $this->cleanupTemporaryFiles();

        return $outputPath;
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
            getenv('ProgramFiles').'\\nodejs\\node.exe',
            getenv('ProgramFiles(x86)').'\\nodejs\\node.exe',
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

        $process = Process::run(['which', 'node']);

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
