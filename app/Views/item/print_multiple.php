<?php
/** @var array $items */
/** @var string $scan_type */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Stickers (<?= count($items) ?> items)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
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

        .btn-print {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
        }
        .btn-print:hover { background-color: #0056b3; }

        .btn-back {
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
        }
        .btn-back:hover { background-color: #545b62; }

        .sticker-preview {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .sticker-grid {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .sticker {
            width: 70mm;
            height: 35mm;
            border: 1.5px solid #333;
            background: white;
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
            page-break-inside: avoid;
            break-inside: avoid;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .s-cell { display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .s-cell:nth-child(odd) { border-right: 1px solid #333; }
        .s-cell:nth-child(1), .s-cell:nth-child(2) { border-bottom: 1px solid #333; }

        .s-code { flex-direction: column; padding: 0; overflow: hidden; }
        .s-code .sc-article {
            flex: 1; display: flex; align-items: center; justify-content: flex-start;
            background-color: #e07b00; color: white; font-weight: 900; font-size: 13px;
            letter-spacing: 0.5px; width: 100%; padding: 2px 6px; word-break: break-all;
        }
        .s-code .sc-category {
            flex: 1; display: flex; align-items: center; justify-content: flex-start;
            background-color: white; color: #333; font-weight: 900; font-size: 9px;
            text-transform: uppercase; width: 100%; padding: 2px 6px;
            border-top: 1px solid #333; word-break: break-all;
        }

        .s-image { background-color: #f8f8f8; padding: 3px; }
        .s-image img { max-width: 100%; max-height: 100%; object-fit: contain; }
        .s-image .no-img { color: #bbb; font-size: 8px; }

        .s-info { flex-direction: column; align-items: flex-start; padding: 0; gap: 0; }
        .s-info .si-body { display: flex; flex-direction: row; flex: 1; width: 100%; overflow: hidden; }
        .s-info .si-left {
            flex: 1; display: flex; flex-direction: column; justify-content: center;
            align-items: center; padding: 3px 4px; border-right: 0.5px solid #ccc; text-align: center;
        }
        .s-info .si-size { font-size: 20px; font-weight: 900; line-height: 1; }
        .s-info .si-size-extra { font-size: 7px; color: #333; line-height: 1.2; }
        .s-info .si-right { flex: 1; display: flex; align-items: center; justify-content: center; padding: 3px 4px; }
        .s-info .si-color { font-size: 9px; font-weight: 900; text-transform: uppercase; color: #111; text-align: center; word-break: break-word; }
        .s-info .si-mrp { font-size: 8px; font-weight: bold; border-top: 0.5px solid #ccc; padding: 2px 4px; width: 100%; display: flex; flex-direction: row; align-items: center; gap: 3px; }
        .s-info .si-mrp .si-mrp-price { font-size: 9px; font-weight: 900; }
        .s-info .si-display-price { font-size: 8px; font-weight: 700; color: #888; text-decoration: line-through; }

        .s-barcode { flex-direction: column; padding: 3px; gap: 1px; }
        .s-barcode svg, .s-barcode canvas, .s-barcode img { max-width: 100%; max-height: 100%; }
        .s-barcode .bc-label { font-size: 5.5px; color: #555; }

        @media print {
            @page { size: 70mm 35mm; margin: 0; padding: 0; }

            body { background-color: white; padding: 0; margin: 0; }

            .print-options, .print-instructions { display: none; }

            .sticker-preview { box-shadow: none; padding: 0; margin: 0; border: none; background: white; }

            .sticker-grid { gap: 0; }

            .sticker { box-shadow: none; page-break-after: always; break-after: page; margin: 0; }
            .sticker:last-child { page-break-after: avoid; break-after: avoid; }
        }
    </style>
</head>
<body>
    <div class="container-fluid" style="max-width:900px; margin:0 auto;">
        <div class="print-options">
            <button class="btn-print" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Print All Stickers (<?= count($items) ?>)
            </button>
            <a href="<?= route_to('item.index') ?>" class="btn-back">
                <i class="fa-solid fa-arrow-left"></i> Back to Items
            </a>
        </div>

        <div class="print-instructions" style="background:#e7f3ff; border:1px solid #b3d9ff; padding:15px; border-radius:5px; margin-bottom:20px;">
            <p style="margin:0; font-size:13px; color:#004085;">
                <strong>Print Instructions:</strong><br>
                Paper Size: <strong>70mm × 35mm</strong> per sticker &nbsp;|&nbsp;
                Set margins to <strong>0mm</strong> &nbsp;|&nbsp;
                Each sticker prints on its own page
            </p>
        </div>

        <div class="sticker-preview">
            <div class="sticker-grid">
                <?php foreach ($items as $idx => $item):
                    $sizeRaw  = $item['size_from'] ?? '-';
                    preg_match('/^(\S+)\s*(.*)$/s', trim($sizeRaw), $szMatch);
                    $mainSize  = $szMatch[1] ?? $sizeRaw;
                    $extraSize = trim($szMatch[2] ?? '');
                ?>
                <div class="sticker">
                    <!-- Box 1: Article + Category -->
                    <div class="s-cell s-code">
                        <div class="sc-article"><?= esc($item['article'] ?? '') ?></div>
                        <div class="sc-category"><?= esc($item['category_name'] ?? 'PRODUCT') ?></div>
                    </div>

                    <!-- Box 2: Image -->
                    <div class="s-cell s-image">
                        <?php if (!empty($item['imageUrl'])): ?>
                            <img src="<?= esc($item['imageUrl']) ?>" alt="<?= esc($item['product_name']) ?>">
                        <?php else: ?>
                            <span class="no-img">No Image</span>
                        <?php endif; ?>
                    </div>

                    <!-- Box 3: Size + Color + MRP -->
                    <div class="s-cell s-info">
                        <div class="si-body">
                            <div class="si-left">
                                <div class="si-size"><?= esc($mainSize) ?></div>
                                <?php if ($extraSize !== ''): ?>
                                    <div class="si-size-extra"><?= esc($extraSize) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="si-right">
                                <?php if (!empty($item['color_name'])): ?>
                                    <div class="si-color"><?= esc($item['color_name']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="si-mrp">
                            <span>MRP:</span>
                            <?php if (!empty($item['display_price'])): ?>
                                <span class="si-display-price">&#8377;<?= number_format($item['display_price'], 2) ?></span>
                            <?php endif; ?>
                            <span class="si-mrp-price">&#8377;<?= number_format($item['mrp'] ?? 0, 2) ?></span>
                        </div>
                    </div>

                    <!-- Box 4: Barcode / QR -->
                    <div class="s-cell s-barcode" id="barcode-cell-<?= $idx ?>">
                        <?php if (($scan_type ?? 'barcode') === 'qrcode'): ?>
                            <div id="qrcode-<?= $idx ?>"></div>
                        <?php else: ?>
                            <svg id="barcode-<?= $idx ?>"></svg>
                            <span class="bc-label"><?= esc($item['product_code']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php if (($scan_type ?? 'barcode') === 'qrcode'): ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <?php endif; ?>

    <script>
        const scanType = '<?= esc($scan_type ?? 'barcode') ?>';

        window.addEventListener('afterprint', function() { window.close(); });

        document.addEventListener('DOMContentLoaded', function() {
            <?php foreach ($items as $idx => $item): ?>
            <?php if (($scan_type ?? 'barcode') === 'qrcode'): ?>
            new QRCode(document.getElementById('qrcode-<?= $idx ?>'), {
                text: '<?= esc($item['product_code']) ?>',
                width: 64, height: 64,
                colorDark: '#000000', colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M
            });
            <?php else: ?>
            JsBarcode('#barcode-<?= $idx ?>', '<?= esc($item['product_code']) ?>', {
                format: 'CODE128', width: 1.5, height: 32, displayValue: false, margin: 2
            });
            <?php endif; ?>
            <?php endforeach; ?>

            <?php if (($scan_type ?? 'barcode') === 'qrcode'): ?>
            setTimeout(function() { window.print(); }, 400);
            <?php else: ?>
            window.print();
            <?php endif; ?>
        });
    </script>
</body>
</html>
