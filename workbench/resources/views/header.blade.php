<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Header Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            border-bottom: 3px solid #007bff;
            text-align: center;
        }
        .logo {
            color: #007bff;
            font-size: 24px;
            font-weight: bold;
        }
        .subtitle {
            color: #6c757d;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">Laravel PDF Generator</div>
        <div class="subtitle">Powered by Playwright - Test Document</div>
    </div>
    
    <div style="margin: 30px 0;">
        <h1>Sample PDF Document</h1>
        <p>This is a test document to demonstrate the Laravel PDF package functionality.</p>
        <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
        
        <h2>Features Tested</h2>
        <ul>
            <li>HTML to PDF conversion</li>
            <li>Blade template rendering</li>
            <li>Custom styling support</li>
            <li>Dynamic content generation</li>
        </ul>
        
        <div style="background-color: #e9ecef; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <strong>Note:</strong> This content is generated from the header.blade.php template.
        </div>
    </div>
</body>
</html>