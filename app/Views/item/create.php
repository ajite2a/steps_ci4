<?= $this->extend('layouts/app') ?>

<?= $this->section('css') ?>
<style>
    .form-section {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    
    .form-group-compact label {
        font-size: 12px;
        font-weight: 600;
        color: #495057;
        margin-bottom: 3px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    
    .form-group-compact input,
    .form-group-compact select,
    .form-group-compact textarea {
        font-size: 13px;
        padding: 6px 8px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }
    
    .form-group-compact input:focus,
    .form-group-compact select:focus,
    .form-group-compact textarea:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .action-buttons {
        margin-top: 20px;
        display: flex;
        gap: 10px;
        padding-top: 15px;
        border-top: 1px solid #dee2e6;
    }
    
    .required-field::after {
        content: " *";
        color: #dc3545;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="mb-1">
        <h2 class="mb-2">
            <?php if (isset($item) && !empty($item)): ?>
                Edit Item
            <?php else: ?>
                Create New Item
            <?php endif; ?>
        </h2>
        <p class="text-muted mb-0">Manage product information and details</p>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form action="<?php if (isset($item) && !empty($item)): ?><?= route_to('item.update', $item['id']) ?><?php else: ?><?= route_to('item.store') ?><?php endif; ?>" method="post">
        <?= csrf_field() ?>

        <div class="row">
            <!-- Left Column - Form (4 columns) -->
            <div class="col-md-4">
                <div class="form-section">
                    <!-- Product Code -->
                    <div class="mb-3">
                        <label for="product_code" class="form-label required-field">Product Code</label>
                        <input type="text" class="form-control form-group-compact" id="product_code" name="product_code" value="<?= isset($item) ? esc($item['product_code']) : '' ?>" placeholder="e.g., 100826" required autofocus>
                    </div>
                    
                    <!-- Date & Product Name -->
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="row">
                                <div class="col-12 mt-3">
                                    <label for="item_date" class="form-label">Date</label>
                                    <input type="date" class="form-control form-group-compact" id="item_date" name="item_date" value="<?= isset($item) ? esc($item['item_date']) : date('Y-m-d') ?>">
                                </div>
                                <div class="col-12 mt-3">
                                    <label for="product_name" class="form-label required-field">Product Name</label>
                                    <select class="form-select form-group-compact" id="product_name" name="product_name" required>
                                        <option value="">-- Select --</option>
                                        <option value="KIDS" <?= isset($item) && $item['product_name'] == 'KIDS' ? 'selected' : '' ?>>KIDS</option>
                                        <option value="MEN" <?= isset($item) && $item['product_name'] == 'MEN' ? 'selected' : '' ?>>MEN</option>
                                        <option value="WOMEN" <?= isset($item) && $item['product_name'] == 'WOMEN' ? 'selected' : '' ?>>WOMEN</option>
                                    </select>
                                </div>
                                <div class="col-12 mt-3">
                                    <label for="color_id" class="form-label">Color</label>
                                    <select class="form-select form-group-compact" id="color_id" name="color_id">
                                        <option value="">-- Select --</option>
                                        <?php if (!empty($colors)): ?>
                                            <?php foreach ($colors as $color): ?>
                                                <option value="<?= esc($color['id']) ?>" <?= isset($item) && $item['color_id'] == $color['id'] ? 'selected' : '' ?>>
                                                    <?= esc($color['color_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-12 mt-3">
                                    <label for="article" class="form-label">Article</label>
                                    <input type="text" class="form-control form-group-compact" id="article" name="article" value="<?= isset($item) ? esc($item['article'] ?? '') : '' ?>" placeholder="e.g., BINGO-151">
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="row">
                                <div class="col-12 mt-3">
                                    <label for="img_code" class="form-label">IMG Code</label>
                                    <input type="text" class="form-control form-group-compact" id="img_code" name="img_code" value="<?= isset($item) ? esc($item['img_code'] ?? '') : '' ?>" placeholder="">
                                </div>
                                <!-- Product Image -->
                                <div class="col-12 mt-3">
                                    <label class="form-label">Product Image</label>
                                    <div style="width: 100%; height: 175px; background: #e9ecef; border: 2px dashed #adb5bd; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #6c757d; font-size: 13px; text-align: center;">
                                        <div>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" style="opacity: 0.5; margin-bottom: 10px;" viewBox="0 0 16 16">
                                                <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                                                <path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/>
                                            </svg>
                                            <p style="margin: 0;">No Image Uploaded</p>
                                            <small>Upload product image</small>
                                        </div>
                                    </div>
                                </div>
                            </div>                                                
                        </div>
                        
                    </div>

                    <div class="mb-3">
                        <label for="supplier_id" class="form-label">Supplier</label>
                        <select class="form-select form-group-compact" id="supplier_id" name="supplier_id">
                            <option value="">-- Select --</option>
                            <?php if (!empty($suppliers)): ?>
                                <?php foreach ($suppliers as $supplier): ?>
                                    <option value="<?= esc($supplier['id']) ?>" <?= isset($item) && $item['supplier_id'] == $supplier['id'] ? 'selected' : '' ?>>
                                        <?= esc($supplier['supplier_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>                        
                    </div>

                    <!-- Article & Product Group -->
                    <div class="row g-3 mb-3">                        
                        <div class="col-12">
                            <label for="product_group" class="form-label">Product Group</label>
                            <input type="text" class="form-control form-group-compact" id="product_group" name="product_group" value="<?= isset($item) ? esc($item['product_group'] ?? '') : '' ?>" placeholder="e.g., SCHOOL SHOES">
                        </div>
                    </div>

                    <!-- Brand & Heels -->
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="brand" class="form-label">Brand</label>
                            <input type="text" class="form-control form-group-compact" id="brand" name="brand" value="<?= isset($item) ? esc($item['brand'] ?? '') : '' ?>" placeholder="e.g., CAMPUS">
                        </div>
                        <div class="col-6">
                            <label for="heels" class="form-label">Heels</label>
                            <input type="text" class="form-control form-group-compact" id="heels" name="heels" value="<?= isset($item) ? esc($item['heels'] ?? '') : '' ?>" placeholder="e.g., FLAT">
                        </div>
                    </div>

                    <!-- Category & Tags -->
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="category" class="form-label">Category</label>
                            <input type="text" class="form-control form-group-compact" id="category" name="category" value="<?= isset($item) ? esc($item['category'] ?? '') : '' ?>" placeholder="e.g., VELCRO">
                        </div>
                        <div class="col-6">
                            <label for="tags" class="form-label">Tags</label>
                            <input type="text" class="form-control form-group-compact" id="tags" name="tags" value="<?= isset($item) ? esc($item['tags'] ?? '') : '' ?>" placeholder="e.g., IS, NEW">
                        </div>
                    </div>

                    <!-- Purchase Rate & GST -->
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="purchase_rate" class="form-label">Purchase Rate</label>
                            <input type="text" class="form-control form-group-compact" id="purchase_rate" name="purchase_rate" value="<?= isset($item) ? esc($item['purchase_rate'] ?? '') : '' ?>" placeholder="577.50">
                        </div>
                        <div class="col-6">
                            <label for="gst" class="form-label">GST %</label>
                            <input type="text" class="form-control form-group-compact" id="gst" name="gst" value="<?= isset($item) ? esc($item['gst'] ?? '') : '' ?>" placeholder="12.0">
                        </div>
                    </div>

                    <!-- MRP & Purchase Code -->
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="mrp" class="form-label">MRP</label>
                            <input type="text" class="form-control form-group-compact" id="mrp" name="mrp" value="<?= isset($item) ? esc($item['mrp'] ?? '') : '' ?>" placeholder="825.00">
                        </div>
                        <div class="col-6">
                            <label for="purchase_code" class="form-label">Purchase Code</label>
                            <input type="text" class="form-control form-group-compact" id="purchase_code" name="purchase_code" value="<?= isset($item) ? esc($item['purchase_code'] ?? '') : '' ?>" placeholder="977078">
                        </div>
                    </div>

                    <!-- Size From & IMG Code -->
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="size_from" class="form-label">From Size</label>
                            <input type="text" class="form-control form-group-compact" id="size_from" name="size_from" value="<?= isset($item) ? esc($item['size_from'] ?? '') : '' ?>" placeholder="32">
                        </div>
                        <div class="col-6">
                            <label for="img_code" class="form-label">IMG Code</label>
                            <input type="text" class="form-control form-group-compact" id="img_code" name="img_code" value="<?= isset($item) ? esc($item['img_code'] ?? '') : '' ?>" placeholder="">
                        </div>
                    </div>

                    
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button type="submit" class="btn btn-gradient flex-grow-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check me-2" viewBox="0 0 16 16" style="display: inline-block; vertical-align: -3px;">
                            <path d="M10.854 7.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
                        </svg>
                        <?php if (isset($item) && !empty($item)): ?>
                            Update Item
                        <?php else: ?>
                            Create Item
                        <?php endif; ?>
                    </button>
                    <a href="<?= route_to('item.index') ?>" class="btn btn-outline-secondary flex-grow-1 text-dark text-decoration-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x me-2" viewBox="0 0 16 16" style="display: inline-block; vertical-align: -3px;">
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                        </svg>
                        Cancel
                    </a>
                </div>
            </div>

            <!-- Right Column - Table Display Area (8 columns) -->
            <div class="col-md-8">
                <div class="form-section" style="border: 2px dashed #dee2e6; display: flex; align-items: center; justify-content: center; min-height: 600px;">
                    <div style="text-align: center; color: #6c757d;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="mb-3" style="opacity: 0.5;" viewBox="0 0 16 16">
                            <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm15 2h-4V2h4v2zm0 5h-5V5h5v4zm0 5h-4v-4h4v4zM10 5H5v4h5V5z"/>
                        </svg>
                        <h6 style="font-size: 14px; font-weight: 600; color: #495057; margin-bottom: 8px;">Table Display Area</h6>
                        <p style="font-size: 12px; margin: 0;">Reserved for future data table display</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

