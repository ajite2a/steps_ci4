<?php

namespace App\Controllers;

use App\Libraries\AccessDB;

class Item extends BaseController
{
    protected $accessDB;

    public function __construct()
    {
        // Use singleton pattern to reuse connection
        $this->accessDB = AccessDB::getInstance();
    }

    public function index()
    {
        $session = session();
        
        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->route('login');
        }

        $filters = [
            'color_id' => trim((string) $this->request->getGet('color_id')),
            'product_group' => trim((string) $this->request->getGet('product_group')),
            'supplier_id' => trim((string) $this->request->getGet('supplier_id')),
            'tags' => trim((string) $this->request->getGet('tags')),
            'article' => trim((string) $this->request->getGet('article')),
            'brand' => trim((string) $this->request->getGet('brand')),
            'created_from' => trim((string) $this->request->getGet('created_from')),
            'created_to' => trim((string) $this->request->getGet('created_to')),
            'size_from' => trim((string) $this->request->getGet('size_from')),
            'size_to' => trim((string) $this->request->getGet('size_to')),
        ];

        $items = $this->accessDB->getAllItems($filters);
        $allItems = $this->accessDB->getAllItems();

        $articleOptions = array_values(array_unique(array_filter(array_map(
            static fn ($item) => trim((string) ($item['article'] ?? '')),
            $allItems
        ))));
        sort($articleOptions);

        $sizeOptions = array_values(array_unique(array_filter(array_map(
            static fn ($item) => trim((string) ($item['size_from'] ?? '')),
            $allItems
        ))));
        natsort($sizeOptions);
        $sizeOptions = array_values($sizeOptions);

        $data = [
            'items' => $items,
            'filters' => $filters,
            'colors' => $this->accessDB->getAllColors(),
            'suppliers' => $this->accessDB->getAllSuppliers(),
            'brands' => $this->accessDB->getAllBrands(),
            'tags' => $this->accessDB->getAllTags(),
            'product_groups' => $this->accessDB->getAllProductGroups(),
            'articleOptions' => $articleOptions,
            'sizeOptions' => $sizeOptions,
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email')
        ];

        return view('item/index', $data);
    }

    public function create()
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            return redirect()->route('login');
        }

        // Get suppliers and colors for dropdowns
        $suppliers = $this->accessDB->getAllSuppliers();
        $colors = $this->accessDB->getAllColors();
        $brands = $this->accessDB->getAllBrands();
        $heels = $this->accessDB->getAllHeels();
        $tags = $this->accessDB->getAllTags();
        $categories = $this->accessDB->getAllCategories();
        $productGroups = $this->accessDB->getAllProductGroups();
        $sizes = $this->accessDB->getAllSizeMasters();

        $data = [
            'suppliers' => $suppliers,
            'colors' => $colors,
            'brands' => $brands,
            'heels' => $heels,
            'tags' => $tags,
            'categories' => $categories,
            'product_groups' => $productGroups,
            'sizes' => $sizes,
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email')
        ];

        return view('item/create', $data);
    }

    public function store()
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Not logged in']);
            }
            return redirect()->route('login');
        }

        // Log all POST data for debugging
        $allPostData = $this->request->getPost();
        log_message('info', '=== FORM SUBMISSION DEBUG ===');
        log_message('info', 'All POST data: ' . json_encode($allPostData));

        $product_name = $this->request->getPost('product_name');
        $date = $this->request->getPost('item_date');
        $supplier_id = $this->request->getPost('supplier_id');
        $purchase_rate = $this->request->getPost('purchase_rate');
        $gst_type = $this->request->getPost('gst_type');
        $mrp = $this->request->getPost('mrp');
        $purchase_code = $this->request->getPost('purchase_code');

        log_message('info', 'Product Name: ' . ($product_name ?? 'NULL'));
        log_message('info', 'Supplier ID: ' . ($supplier_id ?? 'NULL'));
        log_message('info', 'Date: ' . ($date ?? 'NULL'));

        // Get variants data
        $variants = $this->request->getPost('variants');
        log_message('info', 'Variants Count: ' . (is_array($variants) ? count($variants) : 0));

        // Validate input
        if (empty($product_name)) {
            log_message('error', 'Validation failed: Product name is required');
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Product name is required']);
            }
            // Store form data in session for restoration
            $session->setFlashdata('formData', json_encode([
                'productName' => $product_name,
                'itemDate' => $date,
                'supplierId' => $supplier_id,
                'purchaseRate' => $purchase_rate,
                'gstType' => $gst_type,
                'mrp' => $mrp,
                'purchaseCode' => $purchase_code,
                'variants' => $variants
            ]));
            $session->setFlashdata('error', 'Product name is required');
            return redirect()->route('item.create');
        }

        if (empty($supplier_id)) {
            log_message('error', 'Validation failed: Supplier is required');
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Supplier is required']);
            }
            // Store form data in session for restoration
            $session->setFlashdata('formData', json_encode([
                'productName' => $product_name,
                'itemDate' => $date,
                'supplierId' => $supplier_id,
                'purchaseRate' => $purchase_rate,
                'gstType' => $gst_type,
                'mrp' => $mrp,
                'purchaseCode' => $purchase_code,
                'variants' => $variants
            ]));
            $session->setFlashdata('error', 'Supplier is required');
            return redirect()->route('item.create');
        }

        if (empty($variants) || !is_array($variants)) {
            log_message('error', 'Validation failed: At least one variant is required');
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'At least one variant is required']);
            }
            // Store form data in session for restoration
            $session->setFlashdata('formData', json_encode([
                'productName' => $product_name,
                'itemDate' => $date,
                'supplierId' => $supplier_id,
                'purchaseRate' => $purchase_rate,
                'gstType' => $gst_type,
                'mrp' => $mrp,
                'purchaseCode' => $purchase_code,
                'variants' => $variants
            ]));
            $session->setFlashdata('error', 'At least one variant is required');
            return redirect()->route('item.create');
        }

        try {
            $productsCreated = 0;
            $globalSequence = 0; // Track global sequence across all variants and sizes

            // Process each variant
            foreach ($variants as $variantKey => $variant) {
                // Skip if no sizes selected
                if (empty($variant['variant_sizes_input'])) {
                    continue;
                }

                // Parse variant sizes JSON
                $variantSizes = json_decode($variant['variant_sizes_input'], true);
                if (!is_array($variantSizes)) {
                    continue;
                }

                // Get the base product code from the variant (generated by frontend)
                $baseProductCode = $variant['product_code'] ?? 'UNKNOWN';

                // For each size, create N products based on quantity
                foreach ($variantSizes as $sizeData) {
                    $quantity = isset($sizeData['quantity']) ? (int)$sizeData['quantity'] : 0;
                    
                    if ($quantity <= 0) {
                        continue;
                    }

                    // Create N products for this size-quantity combination
                    for ($i = 1; $i <= $quantity; $i++) {
                        $globalSequence++;
                        // Format: BASEPRODUCTCODE-XXXXX (where XXXXX = 5-digit sequential number)
                        $uniqueProductCode = $baseProductCode . '-' . str_pad($globalSequence, 5, '0', STR_PAD_LEFT);

                        // Create product record
                        $result = $this->accessDB->createItem(
                            $uniqueProductCode,                          // product_code
                            $product_name,                               // product_name
                            $date,                                       // item_date
                            $supplier_id,                                // supplier_id
                            $variant['color_id'] ?? null,                // color_id
                            $variant['article'] ?? null,                 // article
                            $variant['product_group'] ?? null,           // product_group
                            $variant['brand'] ?? null,                   // brand
                            $variant['heels'] ?? null,                   // heels
                            $variant['tags'] ?? null,                    // tags
                            $variant['category'] ?? null,                // category
                            $purchase_rate,                              // purchase_rate
                            $gst_type,                                   // gst
                            $mrp,                                        // mrp
                            $purchase_code,                              // purchase_code
                            $sizeData['name'],                           // size_from (store size name)
                            $variant['image_code'] ?? null               // img_code
                        );

                        if ($result) {
                            $productsCreated++;
                        }
                    }
                }
            }

            if ($productsCreated > 0) {
                log_message('info', "Successfully created $productsCreated product(s)");
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => true, 
                        'message' => "Successfully created $productsCreated product(s)"
                    ]);
                }
                $session->setFlashdata('success', "Successfully created $productsCreated product(s)");
                return redirect()->route('item.index');
            } else {
                log_message('error', 'No products created - variant data issue');
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => 'No products were created. Please check your variant data.']);
                }
                $session->setFlashdata('error', 'No products were created. Please check your variant data.');
                return redirect()->route('item.create');
            }
        } catch (\Exception $e) {
            log_message('error', 'Exception in store(): ' . $e->getMessage());
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
            $session->setFlashdata('error', 'Error: ' . $e->getMessage());
            return redirect()->route('item.create');
        }
    }

    public function edit($id)
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            return redirect()->route('login');
        }

        $item = $this->accessDB->getItemById($id);

        if (!$item) {
            $session->setFlashdata('error', 'Item not found');
            return redirect()->route('item.index');
        }

        // Get suppliers and colors for dropdowns
        $suppliers = $this->accessDB->getAllSuppliers();
        $colors = $this->accessDB->getAllColors();
        $sizes = $this->accessDB->getAllSizeMasters();

        $data = [
            'item' => $item,
            'suppliers' => $suppliers,
            'colors' => $colors,
            'sizes' => $sizes,
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email')
        ];

        return view('item/create', $data);
    }

    public function update($id)
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Not logged in']);
            }
            return redirect()->route('login');
        }

        $product_code = $this->request->getPost('product_code');
        $product_name = $this->request->getPost('product_name');
        $date = $this->request->getPost('item_date');
        $supplier_id = $this->request->getPost('supplier_id');
        $color_id = $this->request->getPost('color_id');
        $article = $this->request->getPost('article');
        $product_group = $this->request->getPost('product_group');
        $brand = $this->request->getPost('brand');
        $heels = $this->request->getPost('heels');
        $tags = $this->request->getPost('tags');
        $category = $this->request->getPost('category');
        $purchase_rate = $this->request->getPost('purchase_rate');
        $gst = $this->request->getPost('gst');
        $mrp = $this->request->getPost('mrp');
        $purchase_code = $this->request->getPost('purchase_code');
        $from_size = $this->request->getPost('size_from');
        $img_code = $this->request->getPost('img_code');

        if (empty($product_code) || empty($product_name)) {
            $message = 'Product code and name are required';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $message]);
            }
            $session->setFlashdata('error', $message);
            return redirect()->route('item.edit', ['id' => $id]);
        }

        // Validate that MRP is not less than GST Value (purchase_rate with GST percentage)
        $purchaseRateNum = (float) ($purchase_rate ?? 0);
        $mrpNum = (float) ($mrp ?? 0);
        $gstPercent = 0.0;
        if (!empty($gst)) {
            $gstPercent = (float) str_replace('%', '', (string) $gst);
        }
        $gstValue = $purchaseRateNum > 0 ? ($purchaseRateNum * (1 + ($gstPercent / 100))) : 0;

        if ($mrpNum > 0 && $gstValue > 0 && $mrpNum < $gstValue) {
            $message = 'MRP cannot be less than GST Value';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $message]);
            }
            $session->setFlashdata('error', $message);
            return redirect()->route('item.edit', ['id' => $id]);
        }

        try {
            $result = $this->accessDB->updateItem(
                $id,
                $product_code,
                $product_name,
                $date,
                $supplier_id,
                $color_id,
                $article,
                $product_group,
                $brand,
                $heels,
                $tags,
                $category,
                $purchase_rate,
                $gst,
                $mrp,
                $purchase_code,
                $from_size,
                $img_code
            );

            if ($result) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => true, 'message' => 'Item updated successfully']);
                }
                $session->setFlashdata('success', 'Item updated successfully');
                return redirect()->route('item.index');
            } else {
                $message = 'Failed to update item';
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => $message]);
                }
                $session->setFlashdata('error', $message);
                return redirect()->route('item.edit', ['id' => $id]);
            }
        } catch (\Exception $e) {
            $message = 'Error: ' . $e->getMessage();
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $message]);
            }
            $session->setFlashdata('error', $message);
            return redirect()->route('item.edit', ['id' => $id]);
        }
    }

    public function delete($id)
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            return redirect()->route('login');
        }

        try {
            $result = $this->accessDB->deleteItem($id);

            if ($result) {
                $session->setFlashdata('success', 'Item deleted successfully');
            } else {
                $session->setFlashdata('error', 'Failed to delete item');
            }
        } catch (\Exception $e) {
            $session->setFlashdata('error', 'Error: ' . $e->getMessage());
        }

        return redirect()->route('item.index');
    }

    /**
     * AJAX endpoint for adding new values to various tables
     */
    public function addItemValue()
    {
        // Only accept POST requests
        if ($this->request->getMethod() !== 'POST') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        try {
            $type = $this->request->getPost('type');
            $value = $this->request->getPost('value');

            // Validate inputs
            if (empty($type) || empty($value)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Type and value are required'
                ]);
            }

            // Call appropriate method based on type
            switch ($type) {
                case 'color':
                    $result = $this->accessDB->addColor($value);
                    break;
                case 'brand':
                    $result = $this->accessDB->addBrand($value);
                    break;
                case 'heels':
                    $result = $this->accessDB->addHeels($value);
                    break;
                case 'tags':
                    $result = $this->accessDB->addTags($value);
                    break;
                case 'category':
                    $result = $this->accessDB->addCategory($value);
                    break;
                case 'product_group':
                    $result = $this->accessDB->addProductGroup($value);
                    break;
                default:
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Invalid item type'
                    ]);
            }

            if ($result) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => ucfirst($type) . ' added successfully',
                    'data' => $result
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to add ' . $type
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function getVariantForm()
    {
        $variantCount = $this->request->getGet('variantCount');
        $productName = $this->request->getGet('productName');
        
        if ($variantCount === null || $variantCount === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid variant count'
            ]);
        }

        // Get all necessary data for variant form
        $colors = $this->accessDB->getAllColors();
        $brands = $this->accessDB->getAllBrands();
        $heels = $this->accessDB->getAllHeels();
        $tags = $this->accessDB->getAllTags();
        $categories = $this->accessDB->getAllCategories();
        $productGroups = $this->accessDB->getAllProductGroups();
        
        // Get sizes for the selected product
        $sizes = [];
        if (!empty($productName)) {
            $sizes = $this->accessDB->getSizesByProductName($productName);
        }

        // Build variant HTML
        $variantHtml = '
            <div class="card border-secondary mb-3 variant-item" id="variant-' . $variantCount . '">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Variant #' . $variantCount . '</span>
                    <button type="button" class="btn btn-sm btn-danger remove-variant" data-variant-id="' . $variantCount . '">Remove</button>
                </div>
                <div class="card-body">
                    <!-- Hidden Product Code Input -->
                    <input type="hidden" class="product-code-hidden" name="variants[' . $variantCount . '][product_code]" value="">
                    
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="row g-3">
                                <!-- Color -->
                                <div class="col-md-6 col-lg-6">
                                    <label class="form-label">Color</label>
                                    <div class="d-flex gap-2">
                                        <select class="form-select select2-variant-color" name="variants[' . $variantCount . '][color_id]">
                                            <option value="">Select Color</option>';
        
        foreach ($colors as $color) {
            $variantHtml .= '<option value="' . esc($color['id']) . '">' . esc($color['color_name']) . '</option>';
        }
        
        $variantHtml .= '
                                        </select>
                                        <button class="btn btn-outline-success flex-shrink-0 add-item-btn" type="button" data-type="color" data-field-id="color_id" data-bs-toggle="modal" data-bs-target="#addItemModal" style="min-width: 40px;">+</button>
                                    </div>
                                </div>

                                <!-- Article -->
                                <div class="col-md-6 col-lg-6">
                                    <label class="form-label">Article</label>
                                    <input type="text" class="form-control" name="variants[' . $variantCount . '][article]" placeholder="e.g., BINGO-151">
                                </div>

                                <!-- Product Group -->
                                <div class="col-md-6 col-lg-6">
                                    <label class="form-label">Product Group</label>
                                    <div class="d-flex gap-2">
                                        <select class="form-select select2-variant-product-group" name="variants[' . $variantCount . '][product_group]">
                                            <option value="">Select Product Group</option>';
        
        foreach ($productGroups as $pg) {
            $variantHtml .= '<option value="' . esc($pg['id']) . '">' . esc($pg['group_name']) . '</option>';
        }
        
        $variantHtml .= '
                                        </select>
                                        <button class="btn btn-outline-success flex-shrink-0 add-item-btn" type="button" data-type="product_group" data-field-id="product_group" data-bs-toggle="modal" data-bs-target="#addItemModal" style="min-width: 40px;">+</button>
                                    </div>
                                </div>

                                <!-- Brand -->
                                <div class="col-md-6 col-lg-6">
                                    <label class="form-label">Brand</label>
                                    <div class="d-flex gap-2">
                                        <select class="form-select select2-variant-brand" name="variants[' . $variantCount . '][brand]">
                                            <option value="">Select Brand</option>';
        
        foreach ($brands as $brand) {
            $variantHtml .= '<option value="' . esc($brand['id']) . '">' . esc($brand['brand_name']) . '</option>';
        }
        
        $variantHtml .= '
                                        </select>
                                        <button class="btn btn-outline-success flex-shrink-0 add-item-btn" type="button" data-type="brand" data-field-id="brand" data-bs-toggle="modal" data-bs-target="#addItemModal" style="min-width: 40px;">+</button>
                                    </div>
                                </div>

                                <!-- Heels -->
                                <div class="col-md-6 col-lg-6">
                                    <label class="form-label">Heels</label>
                                    <div class="d-flex gap-2">
                                        <select class="form-select select2-variant-heels" name="variants[' . $variantCount . '][heels]">
                                            <option value="">Select Heels</option>';
        
        foreach ($heels as $heel) {
            $variantHtml .= '<option value="' . esc($heel['id']) . '">' . esc($heel['heel_name']) . '</option>';
        }
        
        $variantHtml .= '
                                        </select>
                                        <button class="btn btn-outline-success flex-shrink-0 add-item-btn" type="button" data-type="heels" data-field-id="heels" data-bs-toggle="modal" data-bs-target="#addItemModal" style="min-width: 40px;">+</button>
                                    </div>
                                </div>

                                <!-- Tags -->
                                <div class="col-md-6 col-lg-6">
                                    <label class="form-label">Tags</label>
                                    <div class="d-flex gap-2">
                                        <select class="form-select select2-variant-tags" name="variants[' . $variantCount . '][tags]">
                                            <option value="">Select Tags</option>';
        
        foreach ($tags as $tag) {
            $variantHtml .= '<option value="' . esc($tag['id']) . '">' . esc($tag['tag_name']) . '</option>';
        }
        
        $variantHtml .= '
                                        </select>
                                        <button class="btn btn-outline-success flex-shrink-0 add-item-btn" type="button" data-type="tags" data-field-id="tags" data-bs-toggle="modal" data-bs-target="#addItemModal" style="min-width: 40px;">+</button>
                                    </div>
                                </div>

                                <!-- Category -->
                                <div class="col-md-6 col-lg-6">
                                    <label class="form-label">Category</label>
                                    <div class="d-flex gap-2">
                                        <select class="form-select select2-variant-category" name="variants[' . $variantCount . '][category]">
                                            <option value="">Select Category</option>';
        
        foreach ($categories as $cat) {
            $variantHtml .= '<option value="' . esc($cat['id']) . '">' . esc($cat['category_name']) . '</option>';
        }
        
        $variantHtml .= '
                                        </select>
                                        <button class="btn btn-outline-success flex-shrink-0 add-item-btn" type="button" data-type="category" data-field-id="category" data-bs-toggle="modal" data-bs-target="#addItemModal" style="min-width: 40px;">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <!-- Variant Image -->
                            <div class="mb-3">
                                <label class="form-label">Variant Image</label>
                                <div class="mb-2 text-center" style="cursor: pointer;">
                                    <img class="variant-image-preview img-thumbnail" src="data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22200%22 viewBox=%220 0 200 200%22%3E%3Crect fill=%22%23e9ecef%22 width=%22200%22 height=%22200%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22Arial%22 font-size=%2216%22 fill=%22%23666%22%3ENo Image%3C/text%3E%3C/svg%3E" alt="Preview" style="max-width: 100%; height: 200px; object-fit: cover; border: 2px dashed #ccc; border-radius: 5px; cursor: pointer;">
                                    <input type="file" class="form-control variant-image-input" name="variants[' . $variantCount . '][image]" accept="image/*" style="display: none;">
                                </div>
                            </div>

                            <!-- Image Code -->
                            <div class="mb-3">
                                <label class="form-label">Image Code</label>
                                <input type="text" class="form-control" name="variants[' . $variantCount . '][image_code]" placeholder="e.g., IMG-001">
                            </div>
                        </div>
                    </div>

                    <!-- Sizes with Quantity - Full Width -->
                    <div class="row g-3 mt-2">
                        <div class="col-12">
                            <label class="form-label">Sizes with Quantity</label>
                            <div class="variant-sizes-container">
                                <!-- Sizes with quantity inputs will be populated here in single row -->
                            </div>
                            <!-- Hidden input to store selected sizes with quantities as JSON -->
                            <input type="hidden" name="variants[' . $variantCount . '][variant_sizes_input]" class="variant-sizes-input" value="">
                        </div>
                    </div>
                </div>
            </div>
        ';

        return $this->response->setJSON([
            'success' => true,
            'html' => $variantHtml,
            'sizes' => $sizes
        ]);
    }

    /**
     * Check if an article already exists in the database
     */
    public function checkArticleExists()
    {
        $article = $this->request->getPost('article');
        
        if (empty($article)) {
            return $this->response->setJSON([
                'success' => false,
                'exists' => false,
                'message' => 'Article is empty'
            ]);
        }

        // Check if article exists in database
        $existingItems = $this->accessDB->getItemsByArticle($article);
        
        if (!empty($existingItems)) {
            return $this->response->setJSON([
                'success' => true,
                'exists' => true,
                'message' => 'Article already exists',
                'items' => $existingItems
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'exists' => false,
            'message' => 'Article does not exist'
        ]);
    }

    /**
     * Check if image exists by image code and return preview URL
     */
    public function checkImageExists()
    {
        $imageCode = $this->request->getPost('image_code');
        
        if (empty($imageCode)) {
            return $this->response->setJSON([
                'success' => false,
                'exists' => false,
                'message' => 'Image code is empty'
            ]);
        }

        // Define upload directory (use public folder for web accessibility)
        $uploadDir = FCPATH . 'uploads/images/';
        
        // Sanitize image code to prevent directory traversal
        $imageCode = preg_replace('/[^a-zA-Z0-9_-]/', '', $imageCode);
        
        // Search for image files with this code
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $foundImage = null;
        $imageUrl = null;

        foreach ($imageExtensions as $ext) {
            $filePath = $uploadDir . $imageCode . '.' . $ext;
            if (file_exists($filePath)) {
                $foundImage = $filePath;
                $imageUrl = base_url('uploads/images/' . $imageCode . '.' . $ext);
                break;
            }
        }

        if ($foundImage) {
            return $this->response->setJSON([
                'success' => true,
                'exists' => true,
                'message' => 'Image found',
                'imageUrl' => $imageUrl
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'exists' => false,
            'message' => 'Image does not exist'
        ]);
    }

    /**
     * Upload variant image
     */
    public function uploadVariantImage()
    {
        $imageCode = $this->request->getPost('image_code');
        $file = $this->request->getFile('image');

        if (empty($imageCode) || !$file || !$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid image code or file'
            ]);
        }

        try {
            // Define upload directory (use public folder for web accessibility)
            $uploadDir = FCPATH . 'uploads/images/';
            
            // Create directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Sanitize image code
            $imageCode = preg_replace('/[^a-zA-Z0-9_-]/', '', $imageCode);
            
            // Get file extension
            $ext = $file->getClientExtension();
            
            // Validate file extension
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array(strtolower($ext), $allowedExtensions)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid file type. Allowed: JPG, PNG, GIF, WebP'
                ]);
            }

            // Delete existing file if it exists
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            foreach ($imageExtensions as $oldExt) {
                $oldFilePath = $uploadDir . $imageCode . '.' . $oldExt;
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            // Move file
            $newFileName = $imageCode . '.' . $ext;
            $file->move($uploadDir, $newFileName);

            $imageUrl = base_url('uploads/images/' . $newFileName);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'imageUrl' => $imageUrl,
                'fileName' => $newFileName
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error uploading image: ' . $e->getMessage()
            ]);
        }
    }

    public function getItemData($id)
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not logged in']);
        }

        try {
            $item = $this->accessDB->getItemById($id);

            if (!$item) {
                return $this->response->setJSON(['success' => false, 'message' => 'Item not found']);
            }

            // Get all reference data for dropdowns
            $colors = $this->accessDB->getAllColors();
            $brands = $this->accessDB->getAllBrands();
            $heels = $this->accessDB->getAllHeels();
            $tags = $this->accessDB->getAllTags();
            $categories = $this->accessDB->getAllCategories();
            $productGroups = $this->accessDB->getAllProductGroups();
            $suppliers = $this->accessDB->getAllSuppliers();
            $productNames = $this->accessDB->getAllProductNames();

            return $this->response->setJSON([
                'success' => true,
                'item' => $item,
                'colors' => $colors,
                'brands' => $brands,
                'heels' => $heels,
                'tags' => $tags,
                'categories' => $categories,
                'productGroups' => $productGroups,
                'suppliers' => $suppliers,
                'productNames' => $productNames
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}
