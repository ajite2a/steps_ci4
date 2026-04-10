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
        <h3 class="fw-bold">Create New Products</h3>
        <p class="text-muted mb-0">Add variants and select quantities for each size. Each quantity will create a unique product with a sequential product code.</p>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="alert alert-info alert-dismissible fade show mb-3">
        <i class="bi bi-info-circle"></i> <strong>Auto-save Active:</strong> Your variant data is automatically saved as you type. If an error occurs, your data will be restored automatically.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <form action="<?= route_to('item.store') ?>" method="post" id="mainForm">
        <?= csrf_field() ?>

        <!-- Header Section -->
        <div class="card border-0 shadow-sm header-section p-4 mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="item_date" class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="item_date" name="item_date" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="product_name" class="form-label d-flex gap-2 align-items-center">
                        Product Name <span class="text-danger">*</span>
                    </label>
                    <div class="d-flex gap-2">
                        <select class="form-select" id="product_name" name="product_name" required>
                            <option value="">Select Product Name</option>
                            <?php if (!empty($sizes)): foreach ($sizes as $size): ?>
                            <option value="<?= esc($size['master_name']) ?>" data-id="<?= esc($size['id']) ?>">
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
                        <option value="<?= esc($supplier['id']) ?>">
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
                <h6 class="mb-0 fw-bold">Common Purchase Details</h6>
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
                        <input type="number" class="form-control" id="gst_value" name="gst_value" placeholder="e.g., 18.00" step="0.01" readonly>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <label for="mrp" class="form-label">MRP</label>
                        <input type="number" class="form-control" id="mrp" name="mrp" placeholder="e.g., 500.00" step="0.01">
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <label for="purchase_code" class="form-label">Purchase Code</label>
                        <input type="text" class="form-control" id="purchase_code" name="purchase_code" placeholder="e.g., PO-2024-001">
                    </div>
                </div>
            </div>
        </div>

    <!-- Form Action Buttons -->
        <div class="row g-3 mb-4">
            <div class="col-auto">
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="bi bi-check-circle"></i> Save All Products
                </button>
            </div>
            <div class="col-auto">
                <a href="<?= route_to('item.index') ?>" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
            </div>
            <div class="col-auto">
                <small class="text-muted">
                    <i class="bi bi-check-circle-fill text-success" id="autosaveIndicator" style="display: none;"></i>
                    <span id="autosaveText" style="display: none;">Auto-saved</span>
                </small>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="debugBtn" title="Debug: Show form state" onclick="showDebugInfo()">
                    📋 Debug
                </button>
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
    
    // Utility function to escape HTML
    window.escapeHtml = function(text) {
        if (!text) return '(empty)';
        return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    };
    // Get form data from PHP session (passed via flashdata)
    const formDataFromSession = <?= session()->getFlashdata('formData') ? session()->getFlashdata('formData') : 'null' ?>;
    
    // Function to save variant state to localStorage
    function saveVariantState() {
        const variantState = {
            productName: $('#product_name').val(),
            supplierId: $('#supplier_id').val(),
            itemDate: $('#item_date').val(),
            purchaseRate: $('#purchase_rate').val(),
            gstType: $('#gst_type').val(),
            mrp: $('#mrp').val(),
            purchaseCode: $('#purchase_code').val(),
            variants: []
        };
        
        // Collect all variant data
        $('#variantsList').find('.variant-item').each(function() {
            const variantId = $(this).attr('id').replace('variant-', '');
            const variantData = {};
            
            // Get all input values from this variant
            $(this).find('input, select, textarea').each(function() {
                const name = $(this).attr('name');
                if (name) {
                    variantData[name] = $(this).val();
                }
            });
            
            variantState.variants.push(variantData);
        });
        
        localStorage.setItem('stepsCi4VariantState', JSON.stringify(variantState));
        console.log('Variant state saved to localStorage');
    }
    
    // Function to reset form to defaults (for fresh page visits)
    function resetForm() {
        console.log('Resetting form to defaults');
        $('#product_name').val('');
        $('#supplier_id').val('');
        $('#item_date').val('<?= date('Y-m-d') ?>');
        $('#purchase_rate').val('');
        $('#gst_type').val('');
        $('#gst_value').val('');
        $('#mrp').val('');
        $('#purchase_code').val('');
        $('#variantsList').html('<p class="text-muted">No variants added yet. Click the button below to add variants.</p>');
    }
    
    // Function to restore variant state from session data or localStorage
    function restoreVariantState() {
        let stateData = null;
        
        // Only restore if there's an error message (meaning form submission failed)
        // Do NOT restore on fresh page visits
        const hasError = <?= session()->getFlashdata('error') ? 'true' : 'false' ?>;
        
        if (!hasError) {
            console.log('Fresh page visit - resetting form to defaults');
            resetForm();
            localStorage.removeItem('stepsCi4VariantState');
            return;
        }
        
        console.log('Error found - attempting to restore previous data');
        
        // First, try to restore from session (PHP flashdata)
        if (formDataFromSession) {
            console.log('Restoring from session data');
            try {
                stateData = JSON.parse(formDataFromSession);
            } catch(e) {
                console.error('Error parsing session data:', e);
            }
        }
        
        // If no session data, try localStorage
        if (!stateData) {
            console.log('Checking localStorage');
            const saved = localStorage.getItem('stepsCi4VariantState');
            if (saved) {
                console.log('Restoring from localStorage');
                try {
                    stateData = JSON.parse(saved);
                } catch(e) {
                    console.error('Error parsing localStorage data:', e);
                }
            }
        }
        
        // Restore the data only if there's an error
        if (stateData && hasError) {
            console.log('Restoring state:', stateData);
            $('#product_name').val(stateData.productName);
            $('#supplier_id').val(stateData.supplierId);
            $('#item_date').val(stateData.itemDate);
            $('#purchase_rate').val(stateData.purchaseRate);
            $('#gst_type').val(stateData.gstType);
            $('#mrp').val(stateData.mrp);
            $('#purchase_code').val(stateData.purchaseCode);
            
            // Trigger Select2 change to update UI
            setTimeout(function() {
                $('#product_name').trigger('change.select2');
                $('#supplier_id').trigger('change.select2');
                $('#gst_type').trigger('change');
                
                // Recalculate GST after restore
                calculateGSTValue();
            }, 100);
        }
        
        // Always clear localStorage after processing
        localStorage.removeItem('stepsCi4VariantState');
    }
    
    // Restore variants on page load
    setTimeout(function() {
        restoreVariantState();
        
        // Initialize Select2 AFTER restoring state
        setTimeout(function() {
            initializeSelect2();
        }, 200);
    }, 500);
    
    // Prevent default form submission completely
    $('#mainForm').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        console.log('Default form submit prevented - use the Save button instead');
        return false;
    });
    
    // Prevent form submission on Enter key in any field
    $('#mainForm').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    });
    
    // Direct AJAX submission instead of form submit
    $('#submitBtn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.clear();
        console.log('=== SAVE ALL PRODUCTS CLICKED ===');
        
        // Get Select2 values directly
        const productName = $('#product_name').val();
        const supplierId = $('#supplier_id').val();
        const itemDate = $('#item_date').val();
        const purchaseRate = $('#purchase_rate').val();
        const gstType = $('#gst_type').val();
        const mrp = $('#mrp').val();
        const purchaseCode = $('#purchase_code').val();
        
        console.log('Product Name (from Select2):', productName);
        console.log('Supplier ID (from Select2):', supplierId);
        
        // Validate product name
        if (!productName || productName === '') {
            Swal.fire({
                icon: 'error',
                title: 'Product Name Required',
                text: 'Please select a product name from the dropdown.',
                confirmButtonColor: '#667eea'
            });
            return false;
        }
        
        // Validate supplier
        if (!supplierId || supplierId === '') {
            Swal.fire({
                icon: 'error',
                title: 'Supplier Required',
                text: 'Please select a supplier from the dropdown.',
                confirmButtonColor: '#667eea'
            });
            return false;
        }
        
        // Get variants
        const variantCount = $('#variantsList').find('.variant-item').length;
        console.log('Variant Count:', variantCount);
        
        if (variantCount === 0) {
            Swal.fire({
                icon: 'error',
                title: 'No Variants',
                text: 'Please add at least one variant with sizes before submitting.',
                confirmButtonColor: '#667eea'
            });
            return false;
        }
        
        // Collect variant data
        const variants = [];
        $('#variantsList').find('.variant-item').each(function(index) {
            const variantIndex = $(this).attr('id').replace('variant-', ''); // Get variant number from id
            
            // Get all form fields for this variant
            const colorId = $(this).find(`[name="variants[${variantIndex}][color_id]"]`).val();
            const sizesJson = $(this).find(`[name="variants[${variantIndex}][variant_sizes_input]"]`).val();
            const article = $(this).find(`[name="variants[${variantIndex}][article]"]`).val();
            const productGroup = $(this).find(`[name="variants[${variantIndex}][product_group]"]`).val();
            const brand = $(this).find(`[name="variants[${variantIndex}][brand]"]`).val();
            const heels = $(this).find(`[name="variants[${variantIndex}][heels]"]`).val();
            const tags = $(this).find(`[name="variants[${variantIndex}][tags]"]`).val();
            const category = $(this).find(`[name="variants[${variantIndex}][category]"]`).val();
            const productCode = $(this).find(`[name="variants[${variantIndex}][product_code]"]`).val();
            const imageCode = $(this).find(`[name="variants[${variantIndex}][image_code]"]`).val();
            
            console.log(`Variant ${variantIndex}:`, {
                colorId,
                sizesJson,
                article,
                productGroup,
                brand,
                heels,
                tags,
                category,
                productCode,
                imageCode
            });
            
            if (sizesJson) {
                try {
                    const sizes = JSON.parse(sizesJson);
                    console.log(`Sizes parsed for variant ${variantIndex}:`, sizes);
                    if (sizes.length > 0) {
                        variants.push({
                            color_id: colorId,
                            article: article,
                            product_group: productGroup,
                            brand: brand,
                            heels: heels,
                            tags: tags,
                            category: category,
                            product_code: productCode,
                            image_code: imageCode,
                            sizes: sizes,
                            variant_sizes_input: sizesJson
                        });
                    }
                } catch(e) {
                    console.error('Invalid JSON in sizes for variant ' + variantIndex + ':', sizesJson, e);
                }
            } else {
                console.warn(`No sizes found for variant ${variantIndex}`);
            }
        });
        
        console.log('Total valid variants:', variants.length);
        
        if (variants.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'No Valid Variants',
                text: 'Please add at least one size with quantity to any variant.',
                confirmButtonColor: '#667eea'
            });
            return false;
        }
        
        console.log('All validations passed. Submitting...');
        
        // Prepare FormData for AJAX submission
        const formData = new FormData();
        formData.append('product_name', productName);
        formData.append('supplier_id', supplierId);
        formData.append('item_date', itemDate);
        formData.append('purchase_rate', purchaseRate);
        formData.append('gst_type', gstType);
        formData.append('mrp', mrp);
        formData.append('purchase_code', purchaseCode);
        
        // Add variant data with correct field naming
        variants.forEach((variant, index) => {
            formData.append(`variants[${index}][color_id]`, variant.color_id || '');
            formData.append(`variants[${index}][article]`, variant.article || '');
            formData.append(`variants[${index}][product_group]`, variant.product_group || '');
            formData.append(`variants[${index}][brand]`, variant.brand || '');
            formData.append(`variants[${index}][heels]`, variant.heels || '');
            formData.append(`variants[${index}][tags]`, variant.tags || '');
            formData.append(`variants[${index}][category]`, variant.category || '');
            formData.append(`variants[${index}][product_code]`, variant.product_code || '');
            formData.append(`variants[${index}][image_code]`, variant.image_code || '');
            formData.append(`variants[${index}][variant_sizes_input]`, variant.variant_sizes_input);
        });
        
        
        // Show loading state
        const originalText = $('#submitBtn').html();
        $('#submitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');
        
        // Submit via AJAX
        $.ajax({
            url: '<?= route_to('item.store') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Success:', response);
                // Clear localStorage after successful submission
                localStorage.removeItem('stepsCi4VariantState');
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'All products created successfully!',
                    confirmButtonColor: '#667eea'
                }).then(function() {
                    window.location.href = '<?= route_to('item.index') ?>';
                });
            },
            error: function(xhr, status, error) {
                console.error('Error:', xhr.responseText);
                // Save current state to localStorage before showing error
                saveVariantState();
                
                let errorMsg = 'An error occurred while saving products.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                // Show error but keep form visible with data
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMsg,
                    confirmButtonColor: '#667eea'
                });
            },
            complete: function() {
                // Restore button state
                $('#submitBtn').prop('disabled', false).html(originalText);
            }
        });
        
        return false;
    });
    
    // Function to initialize Select2 for main dropdown fields
    function initializeSelect2() {
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
    }
    
    // Initialize Select2 on page load (will be re-initialized after restore)
    initializeSelect2();

    // Calculate GST Value based on Purchase Rate and GST Type
    function calculateGSTValue() {
        const purchaseRate = parseFloat($('#purchase_rate').val()) || 0;
        const gstType = $('#gst_type').val();
        
        if (purchaseRate > 0 && gstType) {
            // Extract percentage from gst_type (e.g., "12%" -> 12)
            const gstPercentage = parseFloat(gstType.replace('%', ''));
            // Calculate GST value: purchase_rate * (1 + gst_percentage/100)
            const gstValue = purchaseRate * (1 + gstPercentage / 100);
            // Set the gst_value field with 2 decimal places
            $('#gst_value').val(gstValue.toFixed(2));
        }
    }

    // Trigger calculation on purchase rate change
    $('#purchase_rate').on('change', function() {
        calculateGSTValue();
    });

    // Trigger calculation on GST type change
    $('#gst_type').on('change', function() {
        calculateGSTValue();
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

                    // Populate sizes from selected product as badges with quantity inputs
                    const sizesData = response.sizes || [];
                    const $sizesContainer = $(`#variant-${variantCount}`).find('.variant-sizes-container');
                    const $sizesInput = $(`#variant-${variantCount}`).find('.variant-sizes-input');
                    
                    // Create a wrapper div with horizontal flex layout
                    const $flexWrapper = $('<div class="d-flex flex-wrap gap-2"></div>');
                    
                    sizesData.forEach(size => {
                        const displayText = size.new_size ? `${size.size_value} (${size.new_size})` : size.size_value;
                        const badgeHtml = `
                            <div class="size-quantity-group" style="display: flex; align-items: center; gap: 6px; padding: 6px 10px; border: 1px solid #dee2e6; border-radius: 4px; background-color: #f8f9fa;">
                                <label class="form-check-label mb-0" style="display: flex; align-items: center; gap: 4px; cursor: pointer; margin: 0;">
                                    <input type="checkbox" class="form-check-input variant-size-checkbox" value="${size.id}" data-size-name="${size.size_value}" data-size-display="${displayText}" style="margin: 0; cursor: pointer;">
                                    <span>${displayText}</span>
                                </label>
                                <input type="number" class="form-control form-control-sm variant-size-quantity" min="0" max="9999" value="0" placeholder="Qty" style="width: 55px; display: none;">
                            </div>
                        `;
                        $flexWrapper.append(badgeHtml);
                    });
                    
                    $sizesContainer.append($flexWrapper);

                    // Handle size checkbox changes
                    $(`#variant-${variantCount}`).find('.variant-size-checkbox').on('change', function() {
                        const $group = $(this).closest('.size-quantity-group');
                        const $quantityInput = $group.find('.variant-size-quantity');
                        const $variantSizesInput = $(`#variant-${variantCount}`).find('.variant-sizes-input');
                        
                        if ($(this).is(':checked')) {
                            $group.css('background-color', '#d1ecf1').css('border-color', '#0c5460');
                            $quantityInput.show().focus();
                            // Set default quantity to 0
                            $quantityInput.val('0');
                        } else {
                            $group.css('background-color', '#f8f9fa').css('border-color', '#dee2e6');
                            $quantityInput.hide().val('0');
                        }

                        // Update hidden input with JSON data
                        const selectedSizes = [];
                        $(`#variant-${variantCount}`).find('.variant-size-checkbox:checked').each(function() {
                            const $group = $(this).closest('.size-quantity-group');
                            const quantity = parseInt($group.find('.variant-size-quantity').val()) || 0;
                            selectedSizes.push({
                                id: $(this).val(),
                                name: $(this).data('size-display'),
                                quantity: quantity
                            });
                        });
                        $variantSizesInput.val(JSON.stringify(selectedSizes));
                    });

                    // Handle quantity input changes
                    $(`#variant-${variantCount}`).find('.variant-size-quantity').on('change', function() {
                        const $group = $(this).closest('.size-quantity-group');
                        const $variantSizesInput = $(`#variant-${variantCount}`).find('.variant-sizes-input');
                        
                        // Update hidden input with JSON data
                        const selectedSizes = [];
                        $(`#variant-${variantCount}`).find('.variant-size-checkbox:checked').each(function() {
                            const $group = $(this).closest('.size-quantity-group');
                            const quantity = parseInt($group.find('.variant-size-quantity').val()) || 0;
                            selectedSizes.push({
                                id: $(this).val(),
                                name: $(this).data('size-display'),
                                quantity: quantity
                            });
                        });
                        $variantSizesInput.val(JSON.stringify(selectedSizes));
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
        
        // Auto-save state
        saveVariantState();
    });
    
    // Auto-save when input values change
    $(document).on('change keyup', '#product_name, #supplier_id, #item_date, #purchase_rate, #gst_type, #mrp, #purchase_code', function() {
        // Debounce by using a timeout
        clearTimeout(window.autoSaveTimeout);
        window.autoSaveTimeout = setTimeout(function() {
            saveVariantState();
            // Show auto-save indicator
            $('#autosaveIndicator').show();
            $('#autosaveText').show();
            setTimeout(function() {
                $('#autosaveIndicator').fadeOut();
                $('#autosaveText').fadeOut();
            }, 2000);
        }, 500);
    });
    
    // Auto-save when variant fields change
    $(document).on('change', '.variant-item input, .variant-item select, .variant-item textarea', function() {
        clearTimeout(window.autoSaveTimeout);
        window.autoSaveTimeout = setTimeout(function() {
            saveVariantState();
            // Show auto-save indicator
            $('#autosaveIndicator').show();
            $('#autosaveText').show();
            setTimeout(function() {
                $('#autosaveIndicator').fadeOut();
                $('#autosaveText').fadeOut();
            }, 2000);
        }, 500);
    });
    
    // Debug function to show form state
    window.showDebugInfo = function() {
        const debugInfo = {
            productName: $('#product_name').val(),
            supplierId: $('#supplier_id').val(),
            itemDate: $('#item_date').val(),
            purchaseRate: $('#purchase_rate').val(),
            gstType: $('#gst_type').val(),
            mrp: $('#mrp').val(),
            purchaseCode: $('#purchase_code').val(),
            variantCount: $('#variantsList').find('.variant-item').length
        };
        
        console.table(debugInfo);
        
        Swal.fire({
            title: 'Form State Debug',
            html: '<pre style="text-align: left; background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto;">' + 
                  JSON.stringify(debugInfo, null, 2) + '</pre>',
            icon: 'info',
            confirmButtonColor: '#667eea'
        });
    };
});</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
<?= $this->endSection() ?>

