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

        /* ── Sticker: 2×2 grid ── */
        .sticker {
            width: 70mm;
            height: 35mm;
            border: 1.5px solid #333;
            background: white;
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
            page-break-inside: avoid;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        /* shared cell base — no border; outer box provides perimeter */
        .s-cell {
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* inner vertical divider: right edge of column 1 */
        .s-cell:nth-child(odd) {
            border-right: 1px solid #333;
        }

        /* inner horizontal divider: bottom edge of row 1 */
        .s-cell:nth-child(1),
        .s-cell:nth-child(2) {
            border-bottom: 1px solid #333;
        }

        /* Box 1 – article + category (orange) */
        .s-code {
            flex-direction: column;
            padding: 0;
            overflow: hidden;
        }
        .s-code .sc-article {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            background-color: #e07b00;
            color: white;
            font-weight: 900;
            font-size: 13px;
            letter-spacing: 0.5px;
            width: 100%;
            padding: 2px 6px;
            text-align: left;
            word-break: break-all;
        }
        .s-code .sc-category {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            background-color: white;
            color: #333;
            font-weight: 900;
            font-size: 9px;
            text-transform: uppercase;
            width: 100%;
            padding: 2px 6px;
            text-align: left;
            border-top: 1px solid #333;
            word-break: break-all;
        }

        /* Box 2 – product image */
        .s-image {
            background-color: #f8f8f8;
            padding: 3px;
        }
        .s-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .s-image .no-img {
            color: #bbb;
            font-size: 8px;
        }

        /* Box 3 – size info */
        .s-info {
            flex-direction: column;
            align-items: flex-start;
            padding: 0;
            gap: 0;
        }
        .s-info .si-body {
            display: flex;
            flex-direction: row;
            flex: 1;
            width: 100%;
            overflow: hidden;
        }
        .s-info .si-left {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3px 4px;
            border-right: 0.5px solid #ccc;
            text-align: center;
        }
        .s-info .si-size {
            font-size: 20px;
            font-weight: 900;
            line-height: 1;
        }
        .s-info .si-size-extra {
            font-size: 7px;
            color: #333;
            line-height: 1.2;
        }
        .s-info .si-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3px 4px;
        }
        .s-info .si-color {
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            color: #111;
            text-align: center;
            word-break: break-word;
        }
        .s-info .si-mrp {
            font-size: 7.5px;
            font-weight: bold;
            border-top: 0.5px solid #ccc;
            padding: 2px 4px;
            width: 100%;
        }

        /* Box 4 – barcode */
        .s-barcode {
            flex-direction: column;
            padding: 3px;
            gap: 1px;
        }
        .s-barcode svg {
            max-width: 100%;
        }
        .s-barcode .bc-label {
            font-size: 5.5px;
            color: #555;
        }

        @media print {
            @page {
                size: 70mm 35mm;
                margin: 0;
                padding: 0;
            }

            body {
                background-color: white;
                padding: 0;
                margin: 0;
            }

            .container {
                max-width: none;
                margin: 0;
                padding: 0;
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
                • Paper Size: <strong>70mm × 35mm</strong> (thermal printer label size)<br>
                • Set margins to <strong>0mm</strong> for best results<br>
                • To print multiple copies: click Print > change "Copies" to desired number<br>
                • Use your thermal printer settings or custom paper size
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
                <!-- Sticker: 2×2 grid -->
                <div class="sticker">

                    <!-- Box 1: Article + Category (top-left) -->
                    <div class="s-cell s-code">
                        <div class="sc-article"><?= esc($item['article'] ?? '') ?></div>
                        <div class="sc-category"><?= esc($item['category_name'] ?? 'PRODUCT') ?></div>
                    </div>

                    <!-- Box 2: Product Image (top-right) -->
                    <div class="s-cell s-image">
                        <?php if (!empty($imageUrl)): ?>
                            <img src="<?= esc($imageUrl) ?>" alt="<?= esc($item['product_name']) ?>">
                        <?php else: ?>
                            <span class="no-img">No Image</span>
                        <?php endif; ?>
                    </div>

                    <!-- Box 3: Size Info (bottom-left) -->
                    <?php
                        $sizeRaw   = $item['size_from'] ?? '-';
                        preg_match('/^(\S+)\s*(.*)$/s', trim($sizeRaw), $szMatch);
                        $mainSize  = $szMatch[1] ?? $sizeRaw;
                        $extraSize = trim($szMatch[2] ?? '');
                    ?>
                    <div class="s-cell s-info">
                        <div class="si-body">
                            <!-- Left: sizes -->
                            <div class="si-left">
                                <div class="si-size"><?= esc($mainSize) ?></div>
                                <?php if ($extraSize !== ''): ?>
                                    <div class="si-size-extra"><?= esc($extraSize) ?></div>
                                <?php endif; ?>
                            </div>
                            <!-- Right: color -->
                            <div class="si-right">
                                <?php if (!empty($item['color_name'])): ?>
                                    <div class="si-color"><?= esc($item['color_name']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="si-mrp">MRP: &#8377;<?= number_format($item['mrp'] ?? 0, 2) ?></div>
                    </div>

                    <!-- Box 4: Barcode (bottom-right) -->
                    <div class="s-cell s-barcode">
                        <svg id="barcode"></svg>
                        <span class="bc-label"><?= esc($item['product_code']) ?></span>
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
        // Generate barcode
        document.addEventListener('DOMContentLoaded', function() {
            const productCode = '<?= esc($item['product_code']) ?>';
            
            JsBarcode("#barcode", productCode, {
                format: "CODE128",
                width: 1.5,
                height: 32,
                displayValue: false,
                margin: 2
            });
        });
    </script>
</body>
</html>
