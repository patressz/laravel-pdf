# Laravel PDF Generator

[![Tests](https://github.com/patressz/laravel-pdf/actions/workflows/tests.yml/badge.svg)](https://github.com/patressz/laravel-pdf/actions/workflows/tests.yml)

A modern PHP package for generating PDFs from HTML using Playwright in Laravel applications.

## Features

- 🚀 Generate PDFs from Blade views
- 📄 Multiple PDF formats (A0-A6, Letter, Legal, Tabloid, Ledger) - defaults to A4
- 🎨 Full CSS support with Playwright rendering
- 💾 Save to file or return as base64/binary
- 📥 Direct download response
- 🔧 Configurable Node.js binary path

## Requirements

- PHP 8.4+
- Laravel 11.0+
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
use Patressz\LaravelPdf\PdfBuilder;

// Generate PDF from Blade view
$pdf = PdfBuilder::create()
    ->view('invoice', ['user' => $user])
    ->format('A4')
    ->save('invoice.pdf');
```

### Download PDF Response

```php
// Direct download
return PdfBuilder::create()
    ->view('invoice', ['user' => $user])
    ->format('A4')
    ->download('invoice.pdf');
```

### Inline PDF Response

```php
// Display in browser
return PdfBuilder::create()
    ->view('invoice', ['user' => $user])
    ->format('A4');
```

### Base64 Output

```php
// Get PDF as base64 string
$base64 = PdfBuilder::create()
    ->view('invoice', ['user' => $user])
    ->format('A4')
    ->base64();
```

### Binary Output

```php
// Get PDF as binary string
$binaryPdf = PdfBuilder::create()
    ->view('invoice', ['user' => $user])
    ->format('A4')
    ->raw();
```

## Available Formats

The default format is `A4`. You can specify format using either string or enum:

**Available formats:**
- `A0`, `A1`, `A2`, `A3`, `A4`, `A5`, `A6`
- `Letter`, `Legal`, `Tabloid`, `Ledger`

```php
use Patressz\LaravelPdf\Enums\Format;

// Using enum (recommended)
$pdf = PdfBuilder::create()
    ->view('document')
    ->format(Format::A4)
    ->save('document.pdf');

// Using string
$pdf = PdfBuilder::create()
    ->view('document')
    ->format('A4')
    ->save('document.pdf');

// Default format (A4) - no need to specify
$pdf = PdfBuilder::create()
    ->view('document')
    ->save('document.pdf');
```

## Configuration

### Custom Node.js Binary Path

```php
$pdf = PdfBuilder::create()
    ->setNodeBinaryPath('/custom/path/to/node')
    ->view('document')
    ->save('document.pdf');
```

### Custom Headers

```php
$pdf = PdfBuilder::create()
    ->view('document')
    ->addHeaders([
        'Cache-Control' => 'no-cache',
        'X-Custom-Header' => 'value'
    ])
    ->save('document.pdf');
```

## Examples

### Invoice Generation

```php
// resources/views/invoice.blade.php
<!DOCTYPE html>
<html>
<head>
    <title>Invoice #{{ $invoice->number }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .invoice-details { margin: 20px 0; }
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
    
    <!-- Invoice items -->
    @foreach($invoice->items as $item)
        <div class="item">
            {{ $item->description }} - ${{ $item->amount }}
        </div>
    @endforeach
</body>
</html>
```

```php
// Controller
public function downloadInvoice(Invoice $invoice)
{
    return PdfBuilder::create()
        ->view('invoice', compact('invoice'))
        ->format('A4')
        ->download("invoice-{$invoice->number}.pdf");
}
```

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Patrik Strišovský](https://github.com/patressz)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.