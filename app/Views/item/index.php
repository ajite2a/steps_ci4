<?= $this->extend('layouts/app') ?>

<?= $this->section('css') ?>
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.10/dist/sweetalert2.min.css" rel="stylesheet">
<?= $this->endSection() ?>

<?php
/**
 * Helper function to get image URL by checking all supported extensions
 */
$getImageUrl = function($imageCode) {
    if (empty($imageCode)) {
        return null;
    }
    
    $uploadDir = FCPATH . 'uploads/images/';
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    foreach ($imageExtensions as $ext) {
        $filePath = $uploadDir . $imageCode . '.' . $ext;
        if (file_exists($filePath)) {
            return base_url('uploads/images/' . $imageCode . '.' . $ext);
        }
    }
    
    return null;
};
?>

<?= $this->section('content') ?>

<?php $hasActiveFilters = !empty(array_filter($filters ?? [])); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Items / Products List</h2>
    <a href="<?= route_to('item.create') ?>" class="btn btn-gradient">+ Add Item</a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="get" action="<?= route_to('item.index') ?>">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Color</label>
                    <select name="color_id" class="form-select">
                        <option value="">All Colors</option>
                        <?php foreach ($colors as $color): ?>
                            <option value="<?= esc($color['id']) ?>" <?= (($filters['color_id'] ?? '') == $color['id']) ? 'selected' : '' ?>>
                                <?= esc($color['color_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Group</label>
                    <select name="product_group" class="form-select">
                        <option value="">All Groups</option>
                        <?php foreach ($product_groups as $group): ?>
                            <option value="<?= esc($group['id']) ?>" <?= (($filters['product_group'] ?? '') == $group['id']) ? 'selected' : '' ?>>
                                <?= esc($group['group_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" class="form-select">
                        <option value="">All Suppliers</option>
                        <?php foreach ($suppliers as $supplier): ?>
                            <option value="<?= esc($supplier['id']) ?>" <?= (($filters['supplier_id'] ?? '') == $supplier['id']) ? 'selected' : '' ?>>
                                <?= esc($supplier['supplier_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Tags</label>
                    <select name="tags" class="form-select">
                        <option value="">All Tags</option>
                        <?php foreach ($tags as $tag): ?>
                            <option value="<?= esc($tag['id']) ?>" <?= (($filters['tags'] ?? '') == $tag['id']) ? 'selected' : '' ?>>
                                <?= esc($tag['tag_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Article</label>
                    <select name="article" class="form-select">
                        <option value="">All Articles</option>
                        <?php foreach ($articleOptions as $article): ?>
                            <option value="<?= esc($article) ?>" <?= (($filters['article'] ?? '') === $article) ? 'selected' : '' ?>>
                                <?= esc($article) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Brand</label>
                    <select name="brand" class="form-select">
                        <option value="">All Brands</option>
                        <?php foreach ($brands as $brand): ?>
                            <option value="<?= esc($brand['id']) ?>" <?= (($filters['brand'] ?? '') == $brand['id']) ? 'selected' : '' ?>>
                                <?= esc($brand['brand_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Created Date From</label>
                    <input type="date" name="created_from" class="form-control" value="<?= esc($filters['created_from'] ?? '') ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Created Date To</label>
                    <input type="date" name="created_to" class="form-control" value="<?= esc($filters['created_to'] ?? '') ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Size From</label>
                    <select name="size_from" class="form-select">
                        <option value="">Any</option>
                        <?php foreach ($sizeOptions as $size): ?>
                            <option value="<?= esc($size) ?>" <?= (($filters['size_from'] ?? '') === (string) $size) ? 'selected' : '' ?>>
                                <?= esc($size) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Size To</label>
                    <select name="size_to" class="form-select">
                        <option value="">Any</option>
                        <?php foreach ($sizeOptions as $size): ?>
                            <option value="<?= esc($size) ?>" <?= (($filters['size_to'] ?? '') === (string) $size) ? 'selected' : '' ?>>
                                <?= esc($size) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="<?= route_to('item.index') ?>" class="btn btn-outline-secondary">Clear All</a>
                <span class="btn btn-light disabled">Results: <?= count($items) ?></span>
            </div>
        </form>
    </div>
</div>

<?php if (empty($items)): ?>
    <div class="card text-center">
        <div class="card-body py-5">
            <p class="card-text mb-3"><?= $hasActiveFilters ? 'No items found for the selected filters' : 'No items found' ?></p>
            <?php if ($hasActiveFilters): ?>
                <a href="<?= route_to('item.index') ?>" class="btn btn-outline-secondary">Clear Filters</a>
            <?php else: ?>
                <a href="<?= route_to('item.create') ?>" class="btn btn-gradient">Create First Item</a>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="card ">
        <div class="card-body">
            <div class="table-responsive">
                <table id="itemsTable" class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Image</th>
                            <th>Product Code</th>
                            <th>Product Name</th>
                            <th>Supplier</th>
                            <th>Color</th>
                            <th>Category</th>
                            <th>Purchase Rate</th>
                            <th>MRP</th>
                            <th>Insert Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <?php 
                                $imageUrl = $getImageUrl($item['img_code'] ?? '');
                                $createdDate = !empty($item['created_at']) ? date('d-M-Y', strtotime($item['created_at'])) : '-';
                            ?>
                            <tr>
                                <td>
                                    <?php if (!empty($imageUrl)): ?>
                                        <img src="<?= $imageUrl ?>" 
                                             alt="<?= esc($item['product_name']) ?>" 
                                             style="height: 50px; width: 50px; object-fit: cover; border-radius: 4px; cursor: pointer;" 
                                             class="item-image-preview"
                                             data-bs-toggle="modal" 
                                             data-bs-target="#imageModal"
                                             onclick="showImageModal('<?= $imageUrl ?>', '<?= esc($item['product_name']) ?>')">
                                    <?php else: ?>
                                        <div style="height: 50px; width: 50px; background-color: #e9ecef; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #999; font-size: 12px;">
                                            No Image
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= esc($item['product_code']) ?></strong></td>
                                <td><?= esc($item['product_name']) ?></td>
                                <td><?= esc($item['supplier_name'] ?? '-') ?></td>
                                <td><?= esc($item['color_name'] ?? '-') ?></td>
                                <td><?= esc($item['category'] ?? '-') ?></td>
                                <td><?= esc($item['purchase_rate'] ?? '-') ?></td>
                                <td><?= esc($item['mrp'] ?? '-') ?></td>
                                <td><?= esc($createdDate) ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info edit-item" data-id="<?= $item['id'] ?>" title="Edit Item">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <a href="<?= route_to('item.printSticker', $item['id']) ?>" class="btn btn-sm btn-success print-item" data-id="<?= $item['id'] ?>" title="Print Sticker" target="_blank">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger delete-item" data-id="<?= $item['id'] ?>" data-name="<?= esc($item['product_name']) ?>" title="Delete Item">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Image Preview Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalTitle">Image Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="imageModalImage" src="" alt="Image Preview" style="max-width: 100%; height: auto; border-radius: 8px;">
            </div>
        </div>
    </div>
</div>

<!-- Edit Item Modal -->
<div class="modal fade" id="editItemModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold">Edit Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editItemForm">
                    <input type="hidden" id="editItemId" name="id">
                    <input type="hidden" id="editOriginalProductName" name="original_product_name">
                    <input type="hidden" id="editOriginalSupplierId" name="original_supplier_id">
                    <input type="hidden" id="editOriginalArticle" name="original_article">

                    <!-- Basic Information Section -->
                    <div class="mb-4">
                        <h6 class="bg-light p-2 rounded fw-bold mb-3">Basic Information</h6>
                        <div class="row g-2">
                            <div class="col-md-6 col-lg-3">
                                <label for="editProductCode" class="form-label small">Product Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="editProductCode" name="product_code" readonly>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <label for="editProductName" class="form-label small">Product Name <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm" id="editProductName" name="product_name" required>
                                    <option value="">Select</option>
                                </select>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <label for="editSupplier" class="form-label small">Supplier <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm" id="editSupplier" name="supplier_id" required>
                                    <option value="">Select</option>
                                </select>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <label for="editItemDate" class="form-label small">Item Date</label>
                                <input type="date" class="form-control form-control-sm" id="editItemDate" name="item_date">
                            </div>
                        </div>
                    </div>

                    <!-- Variant Details Section -->
                    <div class="mb-4">
                        <h6 class="bg-light p-2 rounded fw-bold mb-3">Variant Details</h6>
                        <div class="row g-2">
                            <div class="col-lg-8">
                                <div class="row g-2">
                                    <div class="col-md-6 col-lg-4">
                                        <label for="editColor" class="form-label small">Color</label>
                                        <div class="d-flex gap-1">
                                            <select class="form-select form-select-sm" id="editColor" name="color_id">
                                                <option value="">Select</option>
                                            </select>
                                            <button type="button" class="btn btn-sm btn-outline-success flex-shrink-0" data-type="color" data-field-id="editColor" data-bs-toggle="modal" data-bs-target="#addItemModal" style="min-width: 35px;" title="Add Color">+</button>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4">
                                        <label for="editArticle" class="form-label small">Article</label>
                                        <input type="text" class="form-control form-control-sm" id="editArticle" name="article" placeholder="e.g., BINGO-151">
                                    </div>
                                    <div class="col-md-6 col-lg-4">
                                        <label for="editProductGroup" class="form-label small">Product Group</label>
                                        <div class="d-flex gap-1">
                                            <select class="form-select form-select-sm" id="editProductGroup" name="product_group">
                                                <option value="">Select</option>
                                            </select>
                                            <button type="button" class="btn btn-sm btn-outline-success flex-shrink-0" data-type="product_group" data-field-id="editProductGroup" data-bs-toggle="modal" data-bs-target="#addItemModal" style="min-width: 35px;" title="Add Group">+</button>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4">
                                        <label for="editBrand" class="form-label small">Brand</label>
                                        <div class="d-flex gap-1">
                                            <select class="form-select form-select-sm" id="editBrand" name="brand">
                                                <option value="">Select</option>
                                            </select>
                                            <button type="button" class="btn btn-sm btn-outline-success flex-shrink-0" data-type="brand" data-field-id="editBrand" data-bs-toggle="modal" data-bs-target="#addItemModal" style="min-width: 35px;" title="Add Brand">+</button>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4">
                                        <label for="editHeels" class="form-label small">Heels</label>
                                        <div class="d-flex gap-1">
                                            <select class="form-select form-select-sm" id="editHeels" name="heels">
                                                <option value="">Select</option>
                                            </select>
                                            <button type="button" class="btn btn-sm btn-outline-success flex-shrink-0" data-type="heels" data-field-id="editHeels" data-bs-toggle="modal" data-bs-target="#addItemModal" style="min-width: 35px;" title="Add Heels">+</button>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4">
                                        <label for="editTags" class="form-label small">Tags</label>
                                        <div class="d-flex gap-1">
                                            <select class="form-select form-select-sm" id="editTags" name="tags">
                                                <option value="">Select</option>
                                            </select>
                                            <button type="button" class="btn btn-sm btn-outline-success flex-shrink-0" data-type="tags" data-field-id="editTags" data-bs-toggle="modal" data-bs-target="#addItemModal" style="min-width: 35px;" title="Add Tag">+</button>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4">
                                        <label for="editCategory" class="form-label small">Category</label>
                                        <div class="d-flex gap-1">
                                            <select class="form-select form-select-sm" id="editCategory" name="category">
                                                <option value="">Select</option>
                                            </select>
                                            <button type="button" class="btn btn-sm btn-outline-success flex-shrink-0" data-type="category" data-field-id="editCategory" data-bs-toggle="modal" data-bs-target="#addItemModal" style="min-width: 35px;" title="Add Category">+</button>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4">
                                        <label for="editSizeFrom" class="form-label small">Size</label>
                                        <select class="form-select form-select-sm" id="editSizeFrom" name="size_from">
                                            <option value="">Select</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- Image Code and Preview on Right Side -->
                            <div class="col-lg-4">
                                <div class="border rounded p-3" style="background-color: #f8f9fa; height: 100%;">
                                    <label for="editImgCode" class="form-label small d-block mb-2">Image Code</label>
                                    <input type="text" class="form-control form-control-sm mb-3" id="editImgCode" name="img_code" placeholder="e.g., IMG-001">
                                    
                                    <label class="form-label small d-block mb-2">Preview</label>
                                    <div id="editImagePreviewContainer" style="text-align: center; height: 100px; display: flex; align-items: center; justify-content: center; background-color: white; border-radius: 4px; border: 1px solid #dee2e6; overflow: hidden;">
                                        <p class="text-muted small mb-0">No image</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Purchase Details Section -->
                    <div class="mb-4">
                        <h6 class="bg-light p-2 rounded fw-bold mb-3">Purchase Details</h6>
                        <div class="row g-2">
                            <div class="col-md-6 col-lg-3">
                                <label for="editPurchaseRate" class="form-label small">Purchase Rate</label>
                                <input type="number" class="form-control form-control-sm" id="editPurchaseRate" name="purchase_rate" step="0.01" placeholder="0.00">
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <label for="editGST" class="form-label small">GST Type</label>
                                <select class="form-select form-select-sm" id="editGST" name="gst">
                                    <option value="">Select</option>
                                    <option value="5%">5%</option>
                                    <option value="12%">12%</option>
                                    <option value="18%">18%</option>
                                    <option value="28%">28%</option>
                                    <option value="0%">0%</option>
                                </select>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <label for="editGSTValue" class="form-label small">GST Value</label>
                                <input type="number" class="form-control form-control-sm" id="editGSTValue" name="gst_value" step="0.01" placeholder="0.00" readonly>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <label for="editMRP" class="form-label small">MRP</label>
                                <input type="number" class="form-control form-control-sm" id="editMRP" name="mrp" step="0.01" placeholder="0.00">
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <label for="editPurchaseCode" class="form-label small">Purchase Code</label>
                                <input type="text" class="form-control form-control-sm" id="editPurchaseCode" name="purchase_code" placeholder="PO-2024">
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info alert-sm" id="editFormMessage" style="display: none;"></div>

                    <div class="form-check border rounded p-2 bg-light">
                        <input class="form-check-input" type="checkbox" id="editUpdateArticlewise" name="update_articlewise" value="1">
                        <label class="form-check-label" for="editUpdateArticlewise">
                            Update Article-wise (bulk update)
                        </label>
                        <div class="small text-muted">
                            If checked, all items matching original Article + Product Name + Supplier will be updated.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-primary" id="saveEditBtn">
                    <i class="fa-solid fa-floppy-disk"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.10/dist/sweetalert2.all.min.js"></script>
    <script>
        // Function to show image in modal
        window.showImageModal = function(imagePath, productName) {
            $('#imageModalTitle').text(productName);
            $('#imageModalImage').attr('src', imagePath);
            // Modal will be shown automatically via data-bs-toggle
        };

        $(document).ready(function() {
            if ($('#itemsTable').length) {
                $('#itemsTable').DataTable({
                    responsive: true,
                    pageLength: 10,
                    lengthMenu: [5, 10, 25, 50],
                    order: [[1, 'asc']],
                    columnDefs: [
                        {
                            targets: -1,
                            orderable: false,
                            searchable: false
                        },
                        {
                            targets: 0,
                            orderable: false,
                            searchable: false
                        }
                    ]
                });
            }

            // Handle edit button click
            $(document).on('click', '.edit-item', function(e) {
                e.preventDefault();
                const itemId = $(this).data('id');
                openEditModal(itemId);
            });

            // Handle delete button
            $(document).on('click', '.delete-item', function() {
                const itemId = $(this).data('id');
                const itemName = $(this).data('name');

                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to delete "${itemName}". This action cannot be undone!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Create and submit the delete form
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '<?= base_url('items') ?>/' + itemId + '/delete';
                        
                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '<?= csrf_token() ?>';
                        csrfToken.value = '<?= csrf_hash() ?>';
                        
                        form.appendChild(csrfToken);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });

            // Handle save button in edit modal
            $('#saveEditBtn').click(function() {
                saveItemChanges();
            });

            // Handle add item buttons in edit modal
            $(document).on('click', '#editItemModal button[data-type]', function() {
                const type = $(this).data('type');
                const fieldId = $(this).data('field-id');
                window.currentItemType = type;
                window.currentFieldId = fieldId;
                $('#addItemModalTitle').text('Add New ' + type.charAt(0).toUpperCase() + type.slice(1));
                $('#newItemValue').val('').focus();
            });

            // Handle save in add item modal
            $('#saveItemBtn').click(function() {
                const itemType = window.currentItemType;
                const fieldId = window.currentFieldId;
                const itemValue = $('#newItemValue').val().trim();

                if (!itemValue) {
                    alert('Please enter a value');
                    return;
                }

                $.ajax({
                    url: '<?= route_to('item.addItemValue') ?>',
                    type: 'POST',
                    data: {
                        type: itemType,
                        value: itemValue,
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Refresh the dropdown with new value
                            const selectField = $('#' + fieldId);
                            $.ajax({
                                url: '<?= base_url('items') ?>/' + $('#editItemId').val() + '/get-data',
                                type: 'GET',
                                dataType: 'json',
                                success: function(resp) {
                                    if (resp.success) {
                                        // Re-populate the appropriate dropdown
                                        if (itemType === 'color') {
                                            selectField.empty();
                                            selectField.append('<option value="">Select</option>');
                                            if (resp.colors) {
                                                resp.colors.forEach(function(item) {
                                                    selectField.append(`<option value="${item.id}">${item.color_name}</option>`);
                                                });
                                                selectField.val(response.data.id);
                                            }
                                        } else if (itemType === 'brand') {
                                            selectField.empty();
                                            selectField.append('<option value="">Select</option>');
                                            if (resp.brands) {
                                                resp.brands.forEach(function(item) {
                                                    selectField.append(`<option value="${item.id}">${item.brand_name}</option>`);
                                                });
                                                selectField.val(response.data.id);
                                            }
                                        } else if (itemType === 'product_group') {
                                            selectField.empty();
                                            selectField.append('<option value="">Select</option>');
                                            if (resp.productGroups) {
                                                resp.productGroups.forEach(function(item) {
                                                    selectField.append(`<option value="${item.id}">${item.group_name}</option>`);
                                                });
                                                selectField.val(response.data.id);
                                            }
                                        } else if (itemType === 'heels') {
                                            selectField.empty();
                                            selectField.append('<option value="">Select</option>');
                                            if (resp.heels) {
                                                resp.heels.forEach(function(item) {
                                                    selectField.append(`<option value="${item.id}">${item.heel_name}</option>`);
                                                });
                                                selectField.val(response.data.id);
                                            }
                                        } else if (itemType === 'tags') {
                                            selectField.empty();
                                            selectField.append('<option value="">Select</option>');
                                            if (resp.tags) {
                                                resp.tags.forEach(function(item) {
                                                    selectField.append(`<option value="${item.id}">${item.tag_name}</option>`);
                                                });
                                                selectField.val(response.data.id);
                                            }
                                        } else if (itemType === 'category') {
                                            selectField.empty();
                                            selectField.append('<option value="">Select</option>');
                                            if (resp.categories) {
                                                resp.categories.forEach(function(item) {
                                                    selectField.append(`<option value="${item.id}">${item.category_name}</option>`);
                                                });
                                                selectField.val(response.data.id);
                                            }
                                        }
                                    }
                                }
                            });

                            // Close modal and reset
                            bootstrap.Modal.getInstance(document.getElementById('addItemModal')).hide();
                            $('#newItemValue').val('');
                            Swal.fire('Success', response.message, 'success');
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to add item', 'error');
                    }
                });
            });
        });

        // Function to load and display image preview
        window.loadImagePreview = function(imageCode) {
            if (!imageCode) {
                $('#editImagePreviewContainer').html('<p class="text-muted small mb-0">No image</p>');
                return;
            }

            // Sanitize image code
            imageCode = imageCode.trim();
            
            // Check common image extensions
            const extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            const basePath = '<?= base_url('uploads/images/') ?>';
            let imageFound = false;
            let imageUrl = '';

            // Try to load the image with different extensions
            const checkImage = function(index) {
                if (index >= extensions.length) {
                    // No image found
                    if (!imageFound) {
                        $('#editImagePreviewContainer').html('<p class="text-muted small mb-0">No image</p>');
                    }
                    return;
                }

                const ext = extensions[index];
                const testUrl = basePath + imageCode + '.' + ext;
                const img = new Image();

                img.onload = function() {
                    if (!imageFound) {
                        imageFound = true;
                        imageUrl = testUrl;
                        $('#editImagePreviewContainer').html(`<img src="${imageUrl}" alt="Image" style="max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 2px;">`);
                    }
                };

                img.onerror = function() {
                    checkImage(index + 1);
                };

                img.src = testUrl;
            };

            checkImage(0);
        };

        // Calculate GST Value in edit modal, matching create form behavior
        window.calculateEditGSTValue = function() {
            const purchaseRate = parseFloat($('#editPurchaseRate').val()) || 0;
            const gstType = $('#editGST').val();

            if (purchaseRate > 0 && gstType) {
                const gstPercentage = parseFloat(String(gstType).replace('%', '')) || 0;
                const gstValue = purchaseRate * (1 + gstPercentage / 100);
                $('#editGSTValue').val(gstValue.toFixed(2));
            } else {
                $('#editGSTValue').val('');
            }
        };

        // Function to load sizes for a product name
        window.loadSizesForProduct = function(productName, selectedSize = null) {
            if (!productName) {
                $('#editSizeFrom').empty().append('<option value="">Select</option>');
                return;
            }

            $.ajax({
                url: '<?= base_url('item/getVariantForm') ?>',
                type: 'GET',
                data: {
                    variantCount: 0,
                    productName: productName
                },
                dataType: 'json',
                success: function(response) {
                    const sizeSelect = $('#editSizeFrom');
                    sizeSelect.empty().append('<option value="">Select</option>');
                    
                    if (response.success && response.sizes && response.sizes.length > 0) {
                        response.sizes.forEach(size => {
                            const displayText = size.new_size ? `${size.size_value} (${size.new_size})` : size.size_value;
                            sizeSelect.append(`<option value="${size.size_value}">${displayText}</option>`);
                        });
                        
                        // Select the provided size value if given
                        if (selectedSize) {
                            // Try exact match first
                            if (sizeSelect.find('option[value="' + selectedSize + '"]').length > 0) {
                                sizeSelect.val(selectedSize);
                            } else {
                                // If no exact match found, try to find by display text
                                sizeSelect.find('option').each(function() {
                                    if ($(this).val() === selectedSize || $(this).text().includes(selectedSize)) {
                                        sizeSelect.val($(this).val());
                                        return false;
                                    }
                                });
                            }
                            console.log('Size selection attempted:', selectedSize, 'Current value:', sizeSelect.val());
                        }
                    }
                },
                error: function(xhr) {
                    $('#editSizeFrom').empty().append('<option value="">Select</option>');
                    console.error('Error loading sizes:', xhr);
                }
            });
        };

        // Function to open edit modal and load item data
        window.openEditModal = function(itemId) {
            $.ajax({
                url: '<?= base_url('items') ?>/' + itemId + '/get-data',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const item = response.item;
                        
                        // Populate Basic Info fields
                        $('#editItemId').val(item.id);
                        $('#editProductCode').val(item.product_code);
                        $('#editItemDate').val(item.created_at ? item.created_at.split(' ')[0] : '');
                        $('#editOriginalProductName').val(item.product_name || '');
                        $('#editOriginalSupplierId').val(item.supplier_id || '');
                        $('#editOriginalArticle').val(item.article || '');
                        $('#editUpdateArticlewise').prop('checked', false);
                        
                        // Populate Product Name dropdown
                        const productNameSelect = $('#editProductName');
                        productNameSelect.empty();
                        productNameSelect.append('<option value="">Select</option>');
                        if (response.productNames && response.productNames.length > 0) {
                            response.productNames.forEach(function(name) {
                                const isSelected = name === item.product_name ? 'selected' : '';
                                productNameSelect.append(`<option value="${name}" ${isSelected}>${name}</option>`);
                            });
                        }
                        
                        // Populate Supplier dropdown
                        const supplierSelect = $('#editSupplier');
                        supplierSelect.empty();
                        supplierSelect.append('<option value="">Select</option>');
                        if (response.suppliers && response.suppliers.length > 0) {
                            response.suppliers.forEach(function(supplier) {
                                const isSelected = supplier.id == item.supplier_id ? 'selected' : '';
                                supplierSelect.append(`<option value="${supplier.id}" ${isSelected}>${supplier.supplier_name}</option>`);
                            });
                        }
                        
                        // Load sizes based on product name
                        if (item.product_name) {
                            console.log('Loading sizes for product:', item.product_name, 'with size:', item.size_from);
                            loadSizesForProduct(item.product_name, item.size_from);
                        }
                        
                        // Populate Variant Details fields
                        
                        // Color dropdown
                        const colorSelect = $('#editColor');
                        colorSelect.empty();
                        colorSelect.append('<option value="">Select</option>');
                        if (response.colors && response.colors.length > 0) {
                            response.colors.forEach(function(color) {
                                const isSelected = color.id == item.color_id ? 'selected' : '';
                                colorSelect.append(`<option value="${color.id}" ${isSelected}>${color.color_name}</option>`);
                            });
                        }
                        
                        // Article
                        $('#editArticle').val(item.article || '');
                        
                        // Product Group dropdown
                        const pgSelect = $('#editProductGroup');
                        pgSelect.empty();
                        pgSelect.append('<option value="">Select</option>');
                        if (response.productGroups && response.productGroups.length > 0) {
                            response.productGroups.forEach(function(pg) {
                                const isSelected = pg.id == item.product_group ? 'selected' : '';
                                pgSelect.append(`<option value="${pg.id}" ${isSelected}>${pg.group_name}</option>`);
                            });
                        }
                        
                        // Brand dropdown
                        const brandSelect = $('#editBrand');
                        brandSelect.empty();
                        brandSelect.append('<option value="">Select</option>');
                        if (response.brands && response.brands.length > 0) {
                            response.brands.forEach(function(brand) {
                                const isSelected = brand.id == item.brand ? 'selected' : '';
                                brandSelect.append(`<option value="${brand.id}" ${isSelected}>${brand.brand_name}</option>`);
                            });
                        }
                        
                        // Heels dropdown
                        const heelsSelect = $('#editHeels');
                        heelsSelect.empty();
                        heelsSelect.append('<option value="">Select</option>');
                        if (response.heels && response.heels.length > 0) {
                            response.heels.forEach(function(heel) {
                                const isSelected = heel.id == item.heels ? 'selected' : '';
                                heelsSelect.append(`<option value="${heel.id}" ${isSelected}>${heel.heel_name}</option>`);
                            });
                        }
                        
                        // Tags dropdown
                        const tagsSelect = $('#editTags');
                        tagsSelect.empty();
                        tagsSelect.append('<option value="">Select</option>');
                        if (response.tags && response.tags.length > 0) {
                            response.tags.forEach(function(tag) {
                                const isSelected = tag.id == item.tags ? 'selected' : '';
                                tagsSelect.append(`<option value="${tag.id}" ${isSelected}>${tag.tag_name}</option>`);
                            });
                        }
                        
                        // Category dropdown
                        const categorySelect = $('#editCategory');
                        categorySelect.empty();
                        categorySelect.append('<option value="">Select</option>');
                        if (response.categories && response.categories.length > 0) {
                            response.categories.forEach(function(cat) {
                                const isSelected = cat.id == item.category ? 'selected' : '';
                                categorySelect.append(`<option value="${cat.id}" ${isSelected}>${cat.category_name}</option>`);
                            });
                        }
                        
                        // Size From
                        $('#editSizeFrom').val(item.size_from || '');
                        
                        // Image Code
                        $('#editImgCode').val(item.img_code || '');
                        
                        // Load image preview
                        loadImagePreview(item.img_code);
                        
                        // Add event listener for image code changes
                        $('#editImgCode').off('change').on('change', function() {
                            loadImagePreview($(this).val());
                        });
                        
                        // Populate Purchase Details fields
                        $('#editPurchaseRate').val(item.purchase_rate || '');
                        $('#editGST').val(item.gst || '');
                        calculateEditGSTValue();
                        $('#editMRP').val(item.mrp || '');
                        $('#editPurchaseCode').val(item.purchase_code || '');
                        
                        // Show the modal
                        const editModal = new bootstrap.Modal(document.getElementById('editItemModal'));
                        editModal.show();

                        // Add event listener to product name change
                        $('#editProductName').off('change').on('change', function() {
                            const productName = $(this).val();
                            if (productName) {
                                loadSizesForProduct(productName);
                            }
                        });

                        // Keep GST Value synced on field changes
                        $('#editPurchaseRate, #editGST').off('change input').on('change input', function() {
                            calculateEditGSTValue();
                        });

                    } else {
                        Swal.fire('Error', response.message || 'Failed to load item data', 'error');
                    }
                },
                error: function(xhr) {
                    const serverMessage = xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : 'Failed to load item data';
                    Swal.fire('Error', serverMessage, 'error');
                }
            });
        };

        // Function to save changes
        window.saveItemChanges = function() {
            const gstValue = parseFloat($('#editGSTValue').val()) || 0;
            const mrpValue = parseFloat($('#editMRP').val()) || 0;

            if (mrpValue > 0 && gstValue > 0 && mrpValue < gstValue) {
                Swal.fire('Validation Error', 'MRP cannot be less than GST Value', 'warning');
                return;
            }

            const itemId = $('#editItemId').val();
            const formData = {
                product_code: $('#editProductCode').val(),
                product_name: $('#editProductName').val(),
                item_date: $('#editItemDate').val(),
                supplier_id: $('#editSupplier').val(),
                color_id: $('#editColor').val(),
                article: $('#editArticle').val(),
                product_group: $('#editProductGroup').val(),
                brand: $('#editBrand').val(),
                heels: $('#editHeels').val(),
                tags: $('#editTags').val(),
                category: $('#editCategory').val(),
                size_from: $('#editSizeFrom').val(),
                img_code: $('#editImgCode').val(),
                purchase_rate: $('#editPurchaseRate').val(),
                gst: $('#editGST').val(),
                mrp: $('#editMRP').val(),
                purchase_code: $('#editPurchaseCode').val(),
                update_articlewise: $('#editUpdateArticlewise').is(':checked') ? '1' : '0',
                original_product_name: $('#editOriginalProductName').val(),
                original_supplier_id: $('#editOriginalSupplierId').val(),
                original_article: $('#editOriginalArticle').val()
            };

            $.ajax({
                url: '<?= base_url('items') ?>/' + itemId + '/update',
                type: 'POST',
                data: $.extend(formData, {
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                }),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Success',
                            text: response.message || 'Item updated successfully',
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message || 'Failed to update item', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to update item', 'error');
                }
            });
        };
    </script>
<?= $this->endSection() ?>
