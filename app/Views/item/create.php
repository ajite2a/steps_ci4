<?= $this->extend('layouts/app') ?>

<?= $this->section('css') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" />
<style>
    .header-section {
        background-color: #f8f9fa;
        color: white;
    }

    .header-section .form-label {
        color: #495057;
        font-weight: 600;
    }

    .header-section .form-control,
    .header-section .select2-container--bootstrap-5 .select2-selection {
        border-color: transparent;
        box-shadow: none;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="mb-2">
        <h3 class="fw-bold">
            <?= isset($item) && !empty($item) ? 'Edit Item' : 'Create New Item' ?>
        </h3>
        <p class="text-muted mb-0">Manage product information and details</p>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <form action="<?= isset($item) && !empty($item) ? route_to('item.update', $item['id']) : route_to('item.store') ?>" method="post">
        <?= csrf_field() ?>

        <!-- Header Section -->
        <div class="card border-0 shadow-sm header-section p-4 mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="item_date" class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="item_date" name="item_date" value="<?= isset($item) ? esc($item['item_date']) : date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="product_name" class="form-label d-flex gap-2 align-items-center">
                        Product Name <span class="text-danger">*</span>
                    </label>
                    <div class="d-flex gap-2">
                        <select class="form-select" id="product_name" name="product_name" required>
                            <option value="">Select Product Name</option>
                            <?php if (!empty($sizes)): foreach ($sizes as $size): ?>
                            <option value="<?= esc($size['master_name']) ?>" data-id="<?= esc($size['id']) ?>" <?= isset($item) && $item['product_name'] == $size['master_name'] ? 'selected' : '' ?>>
                                <?= esc($size['master_name']) ?>
                            </option>
                            <?php endforeach; endif; ?>
                        </select>
                        <button class="btn btn-outline-secondary flex-shrink-0" type="button" id="sizeInfoBtn" data-bs-toggle="modal" data-bs-target="#sizeModal" title="View Sizes" style="min-width: 40px;">
                            ℹ
                        </button>
                    </div>
                </div>
                <div class="col-md-5">
                    <label for="supplier_id" class="form-label">Supplier Name <span class="text-danger">*</span></label>
                    <select class="form-select" id="supplier_id" name="supplier_id" required>
                        <option value="">Select Supplier</option>
                        <?php if (!empty($suppliers)): foreach ($suppliers as $supplier): ?>
                        <option value="<?= esc($supplier['id']) ?>" <?= isset($item) && $item['supplier_id'] == $supplier['id'] ? 'selected' : '' ?>>
                            <?= esc($supplier['supplier_name']) ?>
                        </option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Variants Section (Collapsible) -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <button class="btn btn-link w-100 text-start text-dark fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#variantsSection">
                    Product Variants <span class="float-end">+</span>
                </button>
            </div>
            <div class="collapse" id="variantsSection">
                <div class="card-body">
                    <div id="variantsList">
                        <p class="text-muted">No variants added yet. Click the button below to add variants.</p>
                    </div>
                    <button type="button" class="btn btn-success" id="addVariantBtn">
                        <i class="bi bi-plus"></i> Add Variant
                    </button>
                </div>
            </div>
        </div>

        <!-- Purchase Details Section -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0 fw-bold">Purchase Details</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6 col-lg-4">
                        <label for="purchase_rate" class="form-label">Purchase Rate</label>
                        <input type="number" class="form-control" id="purchase_rate" name="purchase_rate" placeholder="e.g., 100.00" step="0.01">
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <label for="gst_type" class="form-label">GST Type</label>
                        <select class="form-select" id="gst_type" name="gst_type">
                            <option value="">Select GST Type</option>
                            <option value="5%">5%</option>
                            <option value="12%">12%</option>
                            <option value="18%">18%</option>
                            <option value="28%">28%</option>
                            <option value="0%">0%</option>
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <label for="gst_value" class="form-label">GST Value</label>
                        <input type="number" class="form-control" id="gst_value" name="gst_value" placeholder="e.g., 18.00" step="0.01">
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <label for="purchase_code" class="form-label">Purchase Code</label>
                        <input type="text" class="form-control" id="purchase_code" name="purchase_code" placeholder="e.g., PO-2024-001">
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <label for="mrp" class="form-label">MRP</label>
                        <input type="number" class="form-control" id="mrp" name="mrp" placeholder="e.g., 500.00" step="0.01">
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Action Buttons -->
        <div class="row g-3 mb-4">
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Save
                </button>
            </div>
            <div class="col-auto">
                <a href="<?= route_to('item.index') ?>" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
            </div>
        </div>
    </form>

    <!-- Size Info Modal -->
    <div class="modal fade" id="sizeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Available Sizes - <span id="sizeModalTitle">Select a Product</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="sizeList" class="d-flex flex-wrap gap-2">
                        <p class="text-muted">Please select a product name to view sizes</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Item Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addItemModalTitle">Add New Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control" id="newItemValue" placeholder="Enter value">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveItemBtn">Save Item</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Store sizes data
    const sizesData = <?= json_encode($sizes ?? []) ?>;
    let currentItemType = ''; // Track which type of item is being added
    let currentFieldId = ''; // Track which field to update
    
    // Initialize Select2 for all dropdown fields
    $('#product_name').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });
    
    $('#supplier_id').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });

    $('#color_id').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });

    $('#product_group').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });

    $('#brand').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });

    $('#heels').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });

    $('#tags').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });

    $('#category').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });

    // Handle all add item buttons (use event delegation for dynamic buttons)
    $(document).on('click', '.add-item-btn', function(e) {
        e.stopPropagation();
        currentItemType = $(this).data('type');
        currentFieldId = $(this).data('field-id');
        const displayName = currentItemType.charAt(0).toUpperCase() + currentItemType.slice(1).replace('_', ' ');
        
        $('#addItemModalTitle').text(`Add New ${displayName}`);
        $('#newItemValue').val('').focus();
    });

    // Handle save item button
    $('#saveItemBtn').on('click', function() {
        const itemValue = $('#newItemValue').val().trim();
        
        if (!itemValue) {
            Swal.fire({
                icon: 'warning',
                title: 'Empty Field',
                text: 'Please enter a value',
                confirmButtonColor: '#667eea'
            });
            return;
        }

        // AJAX call to add item to database
        $.ajax({
            type: 'POST',
            url: '<?= base_url('item/addItemValue') ?>',
            data: {
                type: currentItemType,
                value: itemValue
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Add new option to the appropriate select2 dropdown
                    const $select = $(`#${currentFieldId}`);
                    const newOption = new Option(response.data.name, response.data.id, true, true);
                    $select.append(newOption).trigger('change');
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addItemModal'));
                    modal.hide();
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Added!',
                        text: `${currentItemType.charAt(0).toUpperCase() + currentItemType.slice(1)} added successfully`,
                        confirmButtonColor: '#667eea',
                        timer: 1500
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to add item',
                        confirmButtonColor: '#667eea'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while adding the item',
                    confirmButtonColor: '#667eea'
                });
            }
        });
    });

    // Handle product name change
    $('#product_name').on('change', function() {
        const selectedValue = $(this).val();
        const selectedOption = $(this).find('option:selected');
        const selectedId = selectedOption.data('id');
        
        // Check if variants exist
        const variantCount = $('#variantsList').find('.variant-item').length;
        if (variantCount > 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Cannot Change',
                text: 'A variant is already added to this product. Remove the variant first to change the product.',
                confirmButtonColor: '#667eea'
            });
            // Reset to previous value
            $(this).val($(this).data('previous-value')).trigger('change.select2');
            return;
        }
        
        // Store the current value as previous value for next change check
        $(this).data('previous-value', selectedValue);
        
        if (!selectedValue) {
            $('#sizeInfoBtn').prop('disabled', true);
        } else {
            $('#sizeInfoBtn').prop('disabled', false);
        }
    });

    // Handle size info button click
    $('#sizeInfoBtn').on('click', function() {
        const selectedValue = $('#product_name').val();
        const selectedOption = $('#product_name').find('option:selected');
        const selectedId = selectedOption.data('id');
        
        if (!selectedValue) {
            Swal.fire({
                icon: 'warning',
                title: 'Please Select',
                text: 'Please select a product name first',
                confirmButtonColor: '#667eea'
            });
            return;
        }

        // Find the size master data
        const sizeData = sizesData.find(s => s.id == selectedId);
        
        if (sizeData && sizeData.sizes && sizeData.sizes.length > 0) {
            $('#sizeModalTitle').text(selectedValue);
            const sizeHtml = sizeData.sizes.map(size => {
                const displayText = size.new_size 
                    ? `${size.size_value} (${size.new_size})`
                    : size.size_value;
                return `<span class="badge bg-primary">${displayText}</span>`;
            }).join('');
            $('#sizeList').html(sizeHtml);
        } else {
            $('#sizeModalTitle').text(selectedValue);
            $('#sizeList').html('<p class="text-muted">No sizes available for this product</p>');
        }
    });

    // Handle size info button click
    $('#sizeInfoBtn').on('click', function() {
        const selectedValue = $('#product_name').val();
        const selectedOption = $('#product_name').find('option:selected');
        const selectedId = selectedOption.data('id');
        
        if (!selectedValue) {
            Swal.fire({
                icon: 'warning',
                title: 'Please Select',
                text: 'Please select a product name first',
                confirmButtonColor: '#667eea'
            });
            return;
        }

        // Find the size master data
        const sizeData = sizesData.find(s => s.id == selectedId);
        
        if (sizeData && sizeData.sizes && sizeData.sizes.length > 0) {
            $('#sizeModalTitle').text(selectedValue);
            const sizeHtml = sizeData.sizes.map(size => {
                const displayText = size.new_size 
                    ? `${size.size_value} (${size.new_size})`
                    : size.size_value;
                return `<span class="badge bg-primary">${displayText}</span>`;
            }).join('');
            $('#sizeList').html(sizeHtml);
        } else {
            $('#sizeModalTitle').text(selectedValue);
            $('#sizeList').html('<p class="text-muted">No sizes available for this product</p>');
        }
    });

    // Handle add variant button
    let variantCount = 0;
    $('#addVariantBtn').on('click', function(e) {
        e.preventDefault();
        
        // Check if product name is selected
        const productNameValue = $('#product_name').val();
        if (!productNameValue) {
            Swal.fire({
                icon: 'warning',
                title: 'Select Product',
                text: 'Please select a product name first before adding variants.'
            });
            return;
        }
        
        variantCount++;
        
        // Get variant HTML via AJAX
        $.ajax({
            type: 'GET',
            url: '<?= base_url('item/getVariantForm') ?>',
            data: { variantCount: variantCount, productName: productNameValue },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const $variantsList = $('#variantsList');
                    if ($variantsList.find('.variant-item').length === 0) {
                        $variantsList.empty();
                        // Disable product name when first variant is added
                        $('#product_name').prop('disabled', true).data('previous-value', $('#product_name').val());
                    }
                    $variantsList.append(response.html);
                    
                    // Initialize Select2 for newly added select fields
                    $(`#variant-${variantCount}`).find('.select2-variant-color').select2({
                        theme: 'bootstrap-5',
                        width: '100%'
                    });
                    $(`#variant-${variantCount}`).find('.select2-variant-product-group').select2({
                        theme: 'bootstrap-5',
                        width: '100%'
                    });
                    $(`#variant-${variantCount}`).find('.select2-variant-brand').select2({
                        theme: 'bootstrap-5',
                        width: '100%'
                    });
                    $(`#variant-${variantCount}`).find('.select2-variant-heels').select2({
                        theme: 'bootstrap-5',
                        width: '100%'
                    });
                    $(`#variant-${variantCount}`).find('.select2-variant-tags').select2({
                        theme: 'bootstrap-5',
                        width: '100%'
                    });
                    $(`#variant-${variantCount}`).find('.select2-variant-category').select2({
                        theme: 'bootstrap-5',
                        width: '100%'
                    });

                    // Populate sizes from selected product as badges
                    const sizesData = response.sizes || [];
                    const $sizesContainer = $(`#variant-${variantCount}`).find('.variant-sizes-container');
                    const $sizesInput = $(`#variant-${variantCount}`).find('.variant-sizes-input');
                    
                    sizesData.forEach(size => {
                        const badgeHtml = `
                            <label class="badge bg-light text-dark border border-secondary p-2 cursor-pointer" style="cursor: pointer;">
                                <input type="checkbox" class="form-check-input variant-size-checkbox me-1" value="${size.id}" data-size-name="${size.size_value}">
                                ${size.size_value}(${size.new_size ? size.new_size : ''})
                            </label>
                        `;
                        $sizesContainer.append(badgeHtml);
                    });

                    // Handle size checkbox changes
                    $(`#variant-${variantCount}`).find('.variant-size-checkbox').on('change', function() {
                        const $badge = $(this).closest('label');
                        const $variantSizesInput = $(`#variant-${variantCount}`).find('.variant-sizes-input');
                        
                        if ($(this).is(':checked')) {
                            $badge.removeClass('bg-light text-dark border-secondary').addClass('bg-success text-white border-success');
                        } else {
                            $badge.removeClass('bg-success text-white border-success').addClass('bg-light text-dark border-secondary');
                        }

                        const selectedSizes = $(`#variant-${variantCount}`).find('.variant-size-checkbox:checked')
                            .map(function() { return $(this).val(); })
                            .get()
                            .join(',');
                        $variantSizesInput.val(selectedSizes);
                    });

                    // Handle image preview
                    $(`#variant-${variantCount}`).find('.variant-image-input').on('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(event) {
                                $(`#variant-${variantCount}`).find('.variant-image-preview')
                                    .attr('src', event.target.result);
                            };
                            reader.readAsDataURL(file);
                        }
                    });

                    // Handle image click to open file picker
                    $(`#variant-${variantCount}`).find('.variant-image-preview').on('click', function() {
                        $(`#variant-${variantCount}`).find('.variant-image-input').click();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to create variant form.'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while creating variant form.'
                });
            }
        });
    });

    // Handle remove variant
    $(document).on('click', '.remove-variant', function() {
        const variantId = $(this).data('variant-id');
        $(`#variant-${variantId}`).remove();
        
        if ($('#variantsList .variant-item').length === 0) {
            $('#variantsList').html('<p class="text-muted">No variants added yet. Click the button below to add variants.</p>');
            // Re-enable product name selection when all variants are removed
            $('#product_name').prop('disabled', false);
        }
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
<?= $this->endSection() ?>

