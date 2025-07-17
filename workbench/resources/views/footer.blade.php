    <div style="margin-top: 40px;">
        <h2>Additional Information</h2>
        <p>This section demonstrates additional content and styling capabilities.</p>
        
        <div style="display: flex; justify-content: space-between; margin: 20px 0;">
            <div style="flex: 1; margin-right: 20px;">
                <h3>Technical Specifications</h3>
                <ul>
                    <li>PHP Version: {{ PHP_VERSION }}</li>
                    <li>Laravel Framework</li>
                    <li>Playwright PDF Engine</li>
                    <li>Responsive Design</li>
                </ul>
            </div>
            <div style="flex: 1;">
                <h3>Sample Data</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr style="background-color: #f8f9fa;">
                        <th style="border: 1px solid #dee2e6; padding: 8px;">Item</th>
                        <th style="border: 1px solid #dee2e6; padding: 8px;">Value</th>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #dee2e6; padding: 8px;">Test Item 1</td>
                        <td style="border: 1px solid #dee2e6; padding: 8px;">Sample Value</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #dee2e6; padding: 8px;">Test Item 2</td>
                        <td style="border: 1px solid #dee2e6; padding: 8px;">Another Value</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div style="background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <strong>Success:</strong> PDF generation completed successfully using the Laravel PDF package!
        </div>
    </div>
    
    <footer style="margin-top: 50px; padding-top: 20px; border-top: 2px solid #007bff; text-align: center; color: #6c757d;">
        <p>© 2025 Laravel PDF Package - Test Footer Content</p>
        <p style="font-size: 12px;">This footer is generated from footer.blade.php template</p>
    </footer>
</body>
</html>