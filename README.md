# Laravel PDF Generator

[![Tests](https://github.com/patressz/laravel-pdf/actions/workflows/tests.yml/badge.svg)](https://github.com/patressz/laravel-pdf/actions/workflows/tests.yml)

A modern PHP package for generating PDFs from HTML using Playwright in Laravel applications.

## Features

- 🚀 Generate PDFs from Blade views or raw HTML
- 📄 Multiple PDF formats (A0-A6, Letter, Legal, Tabloid, Ledger) - defaults to A4
- 📐 Custom dimensions with flexible units (mm, cm, in, px)
- 🎨 Full CSS support with Playwright rendering
- 🖼️ Print background graphics and images
- 📑 Header and footer templates with dynamic content
- 🔄 Landscape and portrait orientations
- 📏 Configurable margins with multiple units
- 🔍 Scale factor control (0.1-2.0)
- 📖 Tagged (accessible) PDF generation
- 📄 Page range selection
- 💾 Save to file or return as base64/binary
- 📥 Direct download response
- 🔧 Configurable Node.js binary path
- ✂️ Convenient Blade directives for page breaks and numbering
- 🧩 Extensible with custom macros and conditional methods

## Requirements

- PHP 8.3+ or 8.4+
- Laravel 11.0+ or 12.0+
- Node.js 18+
- Yarn or NPM

## Installation

1. Install the package via Composer:

```bash
composer require patressz/laravel-pdf
```

2. Install Node.js dependencies:

```bash
yarn install
# or
npm install
```

3. Install Playwright browser:

```bash
yarn playwright install chromium --with-deps
# or
npx playwright install chromium --with-deps
```

## Usage

### Basic PDF Generation

```php
use Patressz\LaravelPdf\Facades\Pdf;

// Generate PDF from Blade view
$pdf = Pdf::view('invoice', ['user' => $user])
    ->format('A4')
    ->save('invoice.pdf');

// Generate PDF from raw HTML
$pdf = Pdf::html('<h1>Hello World</h1><p>This is a PDF.</p>')
    ->format('A4')
    ->save('document.pdf');
```

### Download PDF response

```php
// Direct download
return Pdf::view('invoice', ['user' => $user])
    ->format('A4')
    ->download('invoice.pdf');
```

### Inline PDF response

```php
// Display in browser
return Pdf::view('invoice', ['user' => $user])
    ->format('A4')
    ->inline('invoice.pdf');
```

### Base64 output

```php
// Get PDF as base64 string
$base64 = Pdf::view('invoice', ['user' => $user])
    ->format('A4')
    ->base64();
```

### Binary output

```php
// Get PDF as binary string
$binaryPdf = Pdf::view('invoice', ['user' => $user])
    ->format('A4')
    ->raw();
```

## Advanced configuration

### Page dimensions and orientation

```php
use Patressz\LaravelPdf\Enums\Format;
use Patressz\LaravelPdf\Enums\Unit;
use Patressz\LaravelPdf\Facades\Pdf;

// Set custom width and height
$pdf = Pdf::view('document')
    ->width(210, Unit::Millimeter)  // or ->width(210, 'mm')
    ->height(297, Unit::Millimeter) // or ->height(297, 'mm')
    ->save('document.pdf');

// Set landscape orientation
$pdf = Pdf::view('document')
    ->format(Format::A4)
    ->landscape()
    ->save('document.pdf');
```

### Margins

```php
// Set margins (top, right, bottom, left)
$pdf = Pdf::view('document')
    ->margins(20, 15, 20, 15, Unit::Millimeter)  // All sides
    ->save('document.pdf');

// Available units: mm, cm, in, px
$pdf = Pdf::view('document')
    ->margins(0.8, 0.6, 0.8, 0.6, Unit::Inch)
    ->save('document.pdf');
```

### Headers and Footers

```php
// Add header template
$pdf = Pdf::view('document')
    ->headerTemplate('<div style="font-size: 10px; text-align: center;">Page <span class="pageNumber"></span> of <span class="totalPages"></span></div>')
    ->save('document.pdf');

// Add footer template
$pdf = Pdf::view('document')
    ->footerTemplate('<div style="font-size: 8px; text-align: center;">Generated on <span class="date"></span></div>')
    ->save('document.pdf');

// Using Blade views for headers/footers
$pdf = Pdf::view('document')
    ->headerTemplate(view('pdf.header'))
    ->footerTemplate(view('pdf.footer'))
    ->save('document.pdf');
```

### Blade directives

Laravel PDF provides convenient Blade directives for PDF generation:

```php
// In your header/footer templates
{{-- Header template with page numbering --}}
<div class="page-header">
    Page @pageNumber of @totalPages
</div>

// In your main document content
{{-- Page break in main content --}}
<div class="section">
    First page content
</div>
@pageBreak
<div class="section">
    Second page content
</div>
```

**header/footer template limitations:**
- Script tags inside templates are not evaluated (no JavaScript)
- Page styles are not visible inside templates (use inline styles)
- Only `@pageNumber` and `@totalPages` directives work in templates
- Main content can use JavaScript and `@pageBreak` directive

**Example usage in header/footer templates:**

```php
// resources/views/pdf/header.blade.php
<div style="text-align: center; font-size: 12px; border-bottom: 1px solid #ccc; padding-bottom: 10px;">
    <h3>{{ $company ?? 'Company Name' }}</h3>
    <p>Page @pageNumber of @totalPages</p>
</div>

// resources/views/pdf/footer.blade.php  
<div style="text-align: center; font-size: 10px; border-top: 1px solid #ccc; padding-top: 10px;">
    <p>Generated on {{ now()->format('Y-m-d H:i:s') }} | Page @pageNumber</p>
</div>

// Using the Pdf facade
Pdf::view('invoice', $data)
    ->headerTemplate(view('pdf.header', ['company' => 'ACME Corp']))
    ->footerTemplate(view('pdf.footer'))
    ->save('invoice.pdf');
```

**Example usage in main document content:**

```html
{{-- resources/views/document.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Multi-page Document</title>
    <script>
        // JavaScript works in main content
        document.addEventListener('DOMContentLoaded', function() {
            // Replace placeholder text with current date
            const placeholder = document.getElementById('current-date');
            if (placeholder) {
                placeholder.textContent = new Date().toLocaleDateString();
            }
        });
    </script>
</head>
<body>
    <div class="section">
        <h1>First Section</h1>
        <p>Generated on: <span id="current-date">[DATE_PLACEHOLDER]</span></p>
        <p>Content for the first page...</p>
    </div>
    
    {{-- Force page break using directive --}}
    @pageBreak
    
    <div class="section">
        <h1>Second Section</h1>
        <p>Content for the second page...</p>
    </div>
    
    @pageBreak
    
    <div class="section">
        <h1>Third Section</h1>
        <p>Content for the third page...</p>
    </div>
</body>
</html>
```

### Print options

```php
$pdf = Pdf::view('document')
    ->printBackground()        // Include background graphics
    ->scale(1.2)              // Scale factor (0.1 - 2.0)
    ->tagged()                // Generate tagged (accessible) PDF
    ->outline()               // Embed document outline
    ->preferCSSPageSize()     // Prefer CSS @page size over format
    ->pageRanges('1-5,8,11-13') // Print specific page ranges
    ->save('document.pdf');
```

## Available formats

The default format is `A4`. You can specify format using either string or enum:

**Available formats:**
- `A0`, `A1`, `A2`, `A3`, `A4`, `A5`, `A6`
- `Letter`, `Legal`, `Tabloid`, `Ledger`

**Available units for dimensions and margins:**
- `mm` (millimeters) - default
- `cm` (centimeters)
- `in` (inches)
- `px` (pixels)

```php
use Patressz\LaravelPdf\Enums\Format;
use Patressz\LaravelPdf\Enums\Unit;
use Patressz\LaravelPdf\Facades\Pdf;

// Using enum (recommended)
$pdf = Pdf::view('document')
    ->format(Format::A4)
    ->save('document.pdf');

// Using string
$pdf = Pdf::view('document')
    ->format('A4')
    ->save('document.pdf');

// Default format (A4) - no need to specify
$pdf = Pdf::view('document')
    ->save('document.pdf');
```

## Configuration

### Download filename

```php
// Set custom filename for downloads
$pdf = Pdf::view('document')
    ->name('my-custom-document.pdf')  // .pdf extension is optional
    ->download();

// Or specify filename in download method
$pdf = Pdf::view('document')
    ->download('invoice-2023.pdf');
```

### Custom Node.js binary path

```php
$pdf = Pdf::setNodeBinaryPath('/custom/path/to/node')
    ->view('document')
    ->save('document.pdf');
```

### Custom response headers

```php
$pdf = Pdf::view('document')
    ->addHeaders([
        'Cache-Control' => 'no-cache',
        'X-Custom-Header' => 'value'
    ])
    ->save('document.pdf');
```

## Examples

### Invoice generation

```php
// resources/views/invoice.blade.php
<!DOCTYPE html>
<html>
<head>
    <title>Invoice #{{ $invoice->number }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .invoice-details { margin: 20px 0; }
        .items { margin: 20px 0; }
        .item { padding: 5px 0; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Invoice #{{ $invoice->number }}</h1>
    </div>
    
    <div class="invoice-details">
        <p><strong>Date:</strong> {{ $invoice->date }}</p>
        <p><strong>Customer:</strong> {{ $invoice->customer->name }}</p>
    </div>
    
    <div class="items">
        <h3>Invoice Items</h3>
        @foreach($invoice->items as $item)
            <div class="item">
                {{ $item->description }} - ${{ $item->amount }}
            </div>
        @endforeach
    </div>
    
    {{-- Use @pageBreak directive for page breaks --}}
    @if($invoice->hasAdditionalPages)
        @pageBreak
        <div class="additional-content">
            {{-- Additional invoice content --}}
        </div>
    @endif
</body>
</html>

{{-- resources/views/pdf/invoice-header.blade.php --}}
<div style="text-align: center; font-size: 12px; border-bottom: 1px solid #ccc; padding: 10px;">
    <strong>{{ $company ?? 'Your Company' }}</strong><br>
    Invoice #{{ $invoice->number }} | Page @pageNumber of @totalPages
</div>

{{-- resources/views/pdf/invoice-footer.blade.php --}}
<div style="text-align: center; font-size: 10px; border-top: 1px solid #ccc; padding: 10px;">
    Generated on {{ now()->format('Y-m-d H:i:s') }} | Page @pageNumber of @totalPages
</div>
```

```php
// Controller
public function downloadInvoice(Invoice $invoice)
{
    return Pdf::view('invoice', compact('invoice'))
        ->format('A4')
        ->margins(20, 15, 20, 15) // 20mm top/bottom, 15mm left/right
        ->printBackground()       // Include background colors/images
        ->headerTemplate(view('pdf.invoice-header', compact('invoice')))
        ->footerTemplate(view('pdf.invoice-footer', compact('invoice')))
        ->download("invoice-{$invoice->number}.pdf");
}

public function generateReport()
{
    return Pdf::view('reports.monthly')
        ->format('A4')
        ->landscape()
        ->margins(15, 10, 15, 10)
        ->scale(0.8)  // Reduce scale to fit more content
        ->save(storage_path('reports/monthly-report.pdf'));
}
```

## Method chaining

All configuration methods return the `PdfBuilder` instance, allowing for fluent method chaining through the `Pdf` facade:

```php
use Patressz\LaravelPdf\Facades\Pdf;

return Pdf::view('complex-document', $data)
    ->format(Format::A4)
    ->landscape()
    ->margins(25, 20, 25, 20, Unit::Millimeter)
    ->printBackground()
    ->scale(1.1)
    ->headerTemplate('<div style="text-align: center; font-size: 12px;">Company Header</div>')
    ->footerTemplate('<div style="text-align: center; font-size: 10px;">Page <span class="pageNumber"></span></div>')
    ->name('complex-document.pdf')
    ->download();
```

## Conditional methods

The underlying `PdfBuilder` class includes Laravel's `Conditionable` trait, which you can access through the `Pdf` facade to conditionally apply methods:

```php
$pdf = Pdf::view('document', $data)
    ->when($user->isPremium(), function ($pdf) {
        return $pdf->headerTemplate(view('pdf.premium-header'));
    })
    ->when($includeFooter, fn($pdf) => $pdf->footerTemplate(view('pdf.footer')))
    ->unless($isPreview, fn($pdf) => $pdf->printBackground())
    ->format(Format::A4)
    ->save('document.pdf');
```

**Available conditional methods:**
- `when($condition, $callback, $default = null)` - Execute callback when condition is true
- `unless($condition, $callback, $default = null)` - Execute callback when condition is false

## Custom macros

The `PdfBuilder` class includes Laravel's `Macroable` trait, allowing you to extend its functionality with custom methods. Both the real `PdfBuilder` and the `FakePdfBuilder` (used in testing) support macros:

```php
use Patressz\LaravelPdf\Facades\Pdf;

// Define a custom macro in a service provider using the facade
Pdf::macro('invoice', function (Invoice $invoice) {
    return $this->view('pdf.invoice', compact('invoice'))
        ->format('A4')
        ->margins(20, 15, 20, 15)
        ->headerTemplate(view('pdf.invoice-header', compact('invoice')))
        ->footerTemplate(view('pdf.invoice-footer'));
});

// Use the macro
$pdf = Pdf::invoice($invoice)->download("invoice-{$invoice->number}.pdf");
```

**Note:** Macros can be defined using either `Pdf::macro()` facade method or directly on the `PdfBuilder` class. Both approaches make the macros available through the `Pdf` facade and work seamlessly with both production code and testing (when using `Pdf::fake()`).

## Troubleshooting

### Node.js not found

If you encounter "Node.js binary not found" errors:

```php
// Set custom Node.js path
$pdf = Pdf::setNodeBinaryPath('/usr/local/bin/node')  // or your custom path
    ->view('document')
    ->save('document.pdf');
```

### Memory issues

For large documents, consider:

- Using `pageRanges()` to process documents in chunks
- Reducing image sizes in your HTML
- Optimizing CSS and avoiding complex layouts

### CSS issues

- Ensure all CSS is inline or included in `<style>` tags
- Use absolute paths for images and assets
- Test with `printBackground()` if backgrounds aren't showing

### JavaScript and Header/Footer limitations

**JavaScript support:**
- ✅ JavaScript works in main document content
- ❌ JavaScript is **not executed** in `headerTemplate()` and `footerTemplate()`
- ❌ Page styles are **not visible** in header/footer templates (use inline styles only)

```php
// ✅ Works - JavaScript in main content
Pdf::html('
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("status").textContent = "Generated successfully";
        });
    </script>
    <h1>Document</h1>
    <p>Status: <span id="status">Loading...</span></p>
')
->save('document.pdf');

// ❌ Won't work - JavaScript in header template
Pdf::view('document')
    ->headerTemplate('<script>document.write("This won\'t work");</script><div>Header</div>')
    ->save('document.pdf');
```

## API Reference

### Content methods

| Method | Parameters | Description |
|--------|------------|-------------|
| `view()` | `string $view, array $data = []` | Render Blade view as PDF content |
| `html()` | `string $html` | Set raw HTML content (takes precedence over view) |

### Template methods

| Method | Parameters | Description |
|--------|------------|-------------|
| `headerTemplate()` | `string\|View $template` | Set header template with special classes |
| `footerTemplate()` | `string\|View $template` | Set footer template with special classes |

### Format & dimensions

| Method | Parameters | Description |
|--------|------------|-------------|
| `format()` | `string\|Format $format` | Set paper format (A0-A6, Letter, Legal, etc.) |
| `width()` | `float $width, Unit\|string $unit = 'mm'` | Set custom width |
| `height()` | `float $height, Unit\|string $unit = 'mm'` | Set custom height |
| `landscape()` | - | Set landscape orientation |
| `margins()` | `float $top, float $right, float $bottom, float $left, Unit\|string $unit = 'mm'` | Set page margins |

### Print options

| Method | Parameters | Description |
|--------|------------|-------------|
| `printBackground()` | - | Include background graphics and colors |
| `displayHeaderFooter()` | - | Enable header/footer display (auto-enabled by templates) |
| `scale()` | `float $scale` | Set scale factor (0.1 - 2.0) |
| `tagged()` | - | Generate tagged (accessible) PDF |
| `outline()` | - | Embed document outline |
| `preferCSSPageSize()` | - | Prefer CSS @page size over format options |
| `pageRanges()` | `string $ranges` | Print specific pages (e.g., '1-5,8,11-13') |

### Output methods

| Method | Parameters | Description |
|--------|------------|-------------|
| `save()` | `string $outputPath` | Save PDF to file and return path |
| `download()` | `string $filename = 'document.pdf'` | Set download headers for attachment |
| `inline()` | `string $filename = 'document.pdf'` | Set inline headers for browser display |
| `base64()` | - | Return PDF as base64 string |
| `raw()` | - | Return PDF as binary string |
| `toResponse()` | `$request` | Return as HTTP response (Responsable interface) |

### Configuration methods

| Method | Parameters | Description |
|--------|------------|-------------|
| `name()` | `string $filename` | Set download filename |
| `setNodeBinaryPath()` | `string $path` | Set custom Node.js binary path |
| `addHeaders()` | `array $headers` | Add custom response headers |

### Extension methods

| Method | Parameters | Description |
|--------|------------|-------------|
| `when()` | `mixed $condition, callable $callback, callable\|null $default` | Execute callback when condition is true |
| `unless()` | `mixed $condition, callable $callback, callable\|null $default` | Execute callback when condition is false |
| `macro()` | `string $name, callable $macro` | **Static method** - Register a custom macro |
| `mixin()` | `object $mixin, bool $replace = true` | **Static method** - Mix another object into the class |
| `hasMacro()` | `string $name` | **Static method** - Check if macro exists |
| `flushMacros()` | - | **Static method** - Remove all macros |

### Blade directives

| Directive | Output | Description |
|-----------|--------|-------------|
| `@pageNumber` | `<span class="pageNumber"></span>` | Current page number (header/footer only) |
| `@totalPages` | `<span class="totalPages"></span>` | Total pages count (header/footer only) |
| `@pageBreak` | `<div style="page-break-after: always;"></div>` | Force page break (main content only) |

**Note:** `@pageNumber` and `@totalPages` only work in `headerTemplate()` and `footerTemplate()` views. `@pageBreak` only works in main document content. Header/footer templates have limitations: no JavaScript execution and no access to page styles.

### Testing PDF generation in your application

Laravel PDF provides a fake implementation for testing without actually generating PDFs:

```php
use Patressz\LaravelPdf\Facades\Pdf;
use Illuminate\View\View;

it('can download invoice', function () {
    // Fake the PDF generation
    Pdf::fake();
    
    $invoice = Invoice::factory()->create();
    
    $response = $this->get("/invoices/{$invoice->id}/download");
    
    $response->assertSuccessful();
    
    // Assert PDF was generated with correct view and data
    Pdf::assertView('invoice', function (View $view, array $data) use ($invoice): bool {
        return $data['invoice']->id === $invoice->id;
    });
    
    // Assert PDF configuration
    Pdf::assertFormat('A4');
    Pdf::assertMargins(20, 15, 20, 15, 'mm');
    Pdf::assertPrintBackground();
    Pdf::assertDownloaded();
});
```

### Available test assertions

The fake PDF implementation provides comprehensive assertions:

```php
// Content assertions
Pdf::assertView('invoice'); // Assert specific view was used
Pdf::assertView('invoice', function (View $view, array $data): bool {
    // Callback receives View object and data array
    return $data['invoice']->total > 100;
});
Pdf::assertHtml('<h1>Test</h1>'); // Assert raw HTML content
Pdf::assertHeaderTemplate($expectedHtml); // Assert header template
Pdf::assertFooterTemplate($expectedHtml); // Assert footer template

// Format and dimensions
Pdf::assertFormat('A4'); // or Format::A4
Pdf::assertWidth(210, 'mm');
Pdf::assertHeight(297, 'mm');
Pdf::assertLandscape(); // Assert landscape is enabled

// Options
Pdf::assertMargins(20, 15, 20, 15, 'mm');
Pdf::assertPrintBackground();
Pdf::assertDisplayHeaderFooter();
Pdf::assertScale(1.2);
Pdf::assertTagged();
Pdf::assertOutline();
Pdf::assertPreferCSSPageSize();
Pdf::assertPageRanges('1-5,8');

// Output assertions
Pdf::assertName('invoice.pdf'); // Assert download filename
Pdf::assertSaved('/path/to/file.pdf'); // Assert file was saved
Pdf::assertDownloaded('invoice.pdf'); // Assert download headers set
Pdf::assertInline('invoice.pdf'); // Assert inline headers set
```

### Testing with callbacks

Use callbacks for more complex assertions:

```php
use Illuminate\View\View;

beforeEach(function () {
    Pdf::fake();
});

it('validates invoice data in PDF', function () {
    $invoice = Invoice::factory()->create(['total' => 150]);
    
    Pdf::view('invoice', compact('invoice'));
    
    // Test view and its data
    Pdf::assertView('invoice', function (View $view, array $data) use ($invoice): bool {
        return $data['invoice']->total > 100 
            && $data['invoice']->id === $invoice->id
            && $view->name() === 'invoice';
    });
});

it('asserts simple view without callback', function () {
    Pdf::view('invoice');
    
    Pdf::assertView('invoice');
});
```
### Testing services that use PDF

When testing components that use PDF generation:

```php
use Illuminate\View\View;

it('generates monthly report', function () {
    Pdf::fake();
    
    $service = new ReportService();
    $service->generateMonthlyReport(2025, 1);
    
    Pdf::assertView('reports.monthly', function (View $view, array $data): bool {
        return $data['month'] === 1 && $data['year'] === 2025;
    });
    Pdf::assertFormat('A4');
    Pdf::assertLandscape(true);
});
```

### Test example with all features

Complete test example showing multiple PDF features:

```php
it('generates complex PDF with multiple features', function () {
    Pdf::fake();
    
    $invoice = Invoice::factory()->create();
    
    Pdf::view('invoice', compact('invoice'))
        ->format(Format::A4)
        ->landscape()
        ->margins(20, 15, 20, 15, Unit::Millimeter)
        ->printBackground()
        ->scale(1.1)
        ->headerTemplate('<div>Header</div>')
        ->footerTemplate('<div>Footer</div>')
        ->name('test-invoice.pdf')
        ->download();
        
    // Assert all configurations
    Pdf::assertView('invoice');
    Pdf::assertFormat('A4');
    Pdf::assertLandscape();
    Pdf::assertMargins(20, 15, 20, 15, 'mm');
    Pdf::assertPrintBackground();
    Pdf::assertScale(1.1);
    Pdf::assertHeaderTemplate('<div>Header</div>');
    Pdf::assertFooterTemplate('<div>Footer</div>');
    Pdf::assertName('test-invoice.pdf');
    Pdf::assertDownloaded('test-invoice.pdf');
});
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Patrik Strišovský](https://github.com/patressz)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
