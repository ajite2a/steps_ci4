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

        $items = $this->accessDB->getAllItems();

        $data = [
            'items' => $items,
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
        $from_size = $this->request->getPost('from_size');
        $img_code = $this->request->getPost('img_code');

        // Validate input
        if (empty($product_code) || empty($product_name)) {
            $session->setFlashdata('error', 'Product code and name are required');
            return redirect()->route('item.create');
        }

        try {
            $result = $this->accessDB->createItem(
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
                $session->setFlashdata('success', 'Item created successfully');
                return redirect()->route('item.index');
            } else {
                $session->setFlashdata('error', 'Failed to create item');
                return redirect()->route('item.create');
            }
        } catch (\Exception $e) {
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
            $session->setFlashdata('error', 'Product code and name are required');
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
                $session->setFlashdata('success', 'Item updated successfully');
                return redirect()->route('item.index');
            } else {
                $session->setFlashdata('error', 'Failed to update item');
                return redirect()->route('item.edit', ['id' => $id]);
            }
        } catch (\Exception $e) {
            $session->setFlashdata('error', 'Error: ' . $e->getMessage());
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
        
        if (empty($variantCount)) {
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
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="row g-3">
                                <!-- Product Code -->
                                <div class="col-md-6 col-lg-6">
                                    <label class="form-label">Product Code</label>
                                    <input type="text" class="form-control" name="variants[' . $variantCount . '][product_code]" placeholder="e.g., PC-001">
                                </div>

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

                            <!-- Sizes -->
                            <div class="mb-3">
                                <label class="form-label">Sizes</label>
                                <div class="variant-sizes-container d-flex flex-wrap gap-2">
                                    <!-- Sizes will be populated here as badges -->
                                </div>
                                <!-- Hidden input to store selected sizes -->
                                <input type="hidden" name="variants[' . $variantCount . '][sizes]" class="variant-sizes-input" value="">
                            </div>
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
}
