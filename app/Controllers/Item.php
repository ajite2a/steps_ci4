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
        $sizes = $this->accessDB->getAllSizeMasters();

        $data = [
            'suppliers' => $suppliers,
            'colors' => $colors,
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
}
