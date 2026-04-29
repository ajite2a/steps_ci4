<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Sticker - <?= esc($item['product_name']) ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- JsBarcode -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .print-options {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .print-options button,
        .print-options a {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .btn-print {
            background-color: #007bff;
            color: white;
        }

        .btn-print:hover {
            background-color: #0056b3;
        }

        .btn-back {
            background-color: #6c757d;
            color: white;
        }

        .btn-back:hover {
            background-color: #545b62;
        }

        .sticker-preview {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .sticker-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
            margin-bottom: 20px;
        }

        .sticker {
            width: 80mm;
            height: 40mm;
            border: 2px solid #333;
            border-radius: 3px;
            padding: 8px;
            background: white;
            position: relative;
            display: flex;
            flex-direction: row;
            gap: 8px;
            page-break-inside: avoid;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin: 0;
        }

        .sticker-left {
            flex: 0.6;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .sticker-code {
            background-color: #ff9800;
            color: white;
            padding: 4px;
            border-radius: 2px;
            font-weight: bold;
            font-size: 11px;
            text-align: center;
            min-height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }

        .sticker-category {
            font-size: 8px;
            font-weight: bold;
            text-align: center;
            border-bottom: 1px solid #333;
            padding-bottom: 2px;
            flex: 0;
        }

        .sticker-size-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-right: 1px solid #333;
            padding-right: 4px;
            gap: 2px;
        }

        .sticker-size-main {
            font-size: 18px;
            font-weight: bold;
            line-height: 1;
            margin-bottom: 2px;
        }

        .sticker-size-variants {
            font-size: 6px;
            line-height: 1.1;
            word-break: break-word;
        }

        .sticker-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .sticker-image-container {
            flex: 1;
            overflow: hidden;
            border-radius: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f9f9f9;
            min-height: 16mm;
        }

        .sticker-image {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .sticker-barcode-container {
            flex: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 10mm;
        }

        .sticker-barcode {
            max-width: 100%;
            height: 100%;
        }

        .sticker-price {
            font-size: 8px;
            text-align: center;
            padding-top: 2px;
            border-top: 1px solid #ccc;
            font-weight: bold;
        }

        @media print {
            @page {
                size: 80mm 40mm;
                margin: 0;
                padding: 0;
            }

            body {
                background-color: white;
                padding: 0;
                margin: 0;
            }

            .print-options {
                display: none;
            }

            .print-instructions {
                display: none;
            }

            .sticker-preview {
                box-shadow: none;
                padding: 0;
                margin: 0;
                border: none;
                background: white;
            }

            .info-panel {
                display: none;
            }

            .sticker-grid {
                margin-bottom: 0;
                gap: 0;
                display: flex;
                flex-direction: column;
            }

            .sticker {
                box-shadow: none;
                page-break-inside: avoid;
                margin: 0;
                break-inside: avoid;
            }
        }

        .info-panel {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .info-panel h3 {
            margin-bottom: 15px;
            color: #333;
            font-size: 16px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: bold;
            color: #666;
        }

        .info-value {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Print Options -->
        <div class="print-options">
            <button class="btn-print" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Print Sticker
            </button>
            <a href="<?= route_to('item.index') ?>" class="btn-back">
                <i class="fa-solid fa-arrow-left"></i> Back to Items
            </a>
        </div>

        <!-- Print Instructions -->
        <div class="print-instructions" style="background: #e7f3ff; border: 1px solid #b3d9ff; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <p style="margin: 0; font-size: 13px; color: #004085;">
                <strong>📋 Print Instructions:</strong><br>
                • Paper Size: <strong>80mm × 40mm</strong> (thermal printer label size)<br>
                • Set margins to <strong>0mm</strong> for best results<br>
                • Use your thermal printer settings or use custom paper size<br>
                • Preview shows 4 stickers - print all or select specific pages
            </p>
        </div>

        <!-- Item Information Panel -->
        <div class="info-panel">
            <h3>Item Details</h3>
            <div class="info-row">
                <span class="info-label">Product Code:</span>
                <span class="info-value"><?= esc($item['product_code']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Product Name:</span>
                <span class="info-value"><?= esc($item['product_name']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Category:</span>
                <span class="info-value"><?= esc($item['category'] ?? 'N/A') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Size:</span>
                <span class="info-value"><?= esc($item['size_from'] ?? 'N/A') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Supplier:</span>
                <span class="info-value"><?= esc($item['supplier_name'] ?? 'N/A') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Color:</span>
                <span class="info-value"><?= esc($item['color_name'] ?? 'N/A') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Purchase Rate:</span>
                <span class="info-value">₹<?= number_format($item['purchase_rate'] ?? 0, 2) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">MRP:</span>
                <span class="info-value">₹<?= number_format($item['mrp'] ?? 0, 2) ?></span>
            </div>
        </div>

        <!-- Sticker Preview -->
        <div class="sticker-preview">
            <div class="sticker-grid">
                <!-- Sticker -->
                <div class="sticker">
                    <!-- Left Section -->
                    <div class="sticker-left">
                        <div class="sticker-code">
                            <?= esc($item['product_code']) ?>
                        </div>
                        <div class="sticker-category">
                            <?= esc($item['category'] ?? 'PRODUCT') ?>
                        </div>
                        <div class="sticker-size-info">
                            <div class="sticker-size-main">
                                <?= esc($item['size_from'] ?? '-') ?>
                            </div>
                            <div class="sticker-size-variants">
                                <div><?= esc($item['color_name'] ?? '') ?></div>
                                <div><?= esc($item['supplier_name'] ?? '') ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Section -->
                    <div class="sticker-right">
                        <!-- Image Container -->
                        <div class="sticker-image-container">
                            <?php if (!empty($imageUrl)): ?>
                                <img src="<?= $imageUrl ?>" alt="<?= esc($item['product_name']) ?>" class="sticker-image">
                            <?php else: ?>
                                <span style="color: #ccc; font-size: 12px;">No Image</span>
                            <?php endif; ?>
                        </div>

                        <!-- Barcode -->
                        <div class="sticker-barcode-container">
                            <svg id="barcode" class="sticker-barcode"></svg>
                        </div>

                        <!-- Price -->
                        <div class="sticker-price">
                            <strong>₹<?= number_format($item['mrp'] ?? 0, 2) ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Generate barcodes
        document.addEventListener('DOMContentLoaded', function() {
            const productCode = '<?= esc($item['product_code']) ?>';
            
            // Generate barcode for all stickers (80x40mm thermal printer format)
            for (let i = 1; i <= 4; i++) {
                const elementId = "#barcode" + (i === 1 ? '' : i);
                try {
                    JsBarcode(elementId, productCode, {
                        format: "CODE128",
                        width: 1.5,
                        height: 32,
                        displayValue: false,
                        margin: 2
                    });
                } catch(e) {
                    console.log('Barcode generation for ' + elementId + ' completed');
                }
            }
        });
    </script>
</body>
</html>
