<?php

namespace App\Controllers;

use App\Libraries\AccessDB;

class Supplier extends BaseController
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

        $suppliers = $this->accessDB->getAllSuppliers();

        $data = [
            'suppliers' => $suppliers,
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email')
        ];

        return view('supplier/index', $data);
    }

    public function create()
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            return redirect()->route('login');
        }

        $data = [
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email')
        ];

        return view('supplier/create', $data);
    }

    public function store()
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            return redirect()->route('login');
        }

        $supplier_name = $this->request->getPost('supplier_name');
        $phone = $this->request->getPost('phone');
        $gst = $this->request->getPost('gst');
        $address = $this->request->getPost('address');

        // Validate input
        if (empty($supplier_name)) {
            $session->setFlashdata('error', 'Supplier name is required');
            return redirect()->route('supplier.create');
        }

        try {
            $result = $this->accessDB->createSupplier($supplier_name, $phone, $gst, $address);

            if ($result) {
                $session->setFlashdata('success', 'Supplier created successfully');
                return redirect()->route('supplier.index');
            } else {
                $session->setFlashdata('error', 'Failed to create supplier');
                return redirect()->route('supplier.create');
            }
        } catch (\Exception $e) {
            $session->setFlashdata('error', 'Error: ' . $e->getMessage());
            return redirect()->route('supplier.create');
        }
    }

    public function edit($id)
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            return redirect()->route('login');
        }

        $supplier = $this->accessDB->getSupplierById($id);

        if (!$supplier) {
            $session->setFlashdata('error', 'Supplier not found');
            return redirect()->route('supplier.index');
        }

        $data = [
            'supplier' => $supplier,
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email')
        ];

        return view('supplier/create', $data);
    }

    public function update($id)
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            return redirect()->route('login');
        }

        $supplier_name = $this->request->getPost('supplier_name');
        $phone = $this->request->getPost('phone');
        $gst = $this->request->getPost('gst');
        $address = $this->request->getPost('address');

        if (empty($supplier_name)) {
            $session->setFlashdata('error', 'Supplier name is required');
            return redirect()->route('supplier.edit', ['id' => $id]);
        }

        try {
            $result = $this->accessDB->updateSupplier($id, $supplier_name, $phone, $gst, $address);

            if ($result) {
                $session->setFlashdata('success', 'Supplier updated successfully');
                return redirect()->route('supplier.index');
            } else {
                $session->setFlashdata('error', 'Failed to update supplier');
                return redirect()->route('supplier.edit', ['id' => $id]);
            }
        } catch (\Exception $e) {
            $session->setFlashdata('error', 'Error: ' . $e->getMessage());
            return redirect()->route('supplier.edit', ['id' => $id]);
        }
    }

    public function delete($id)
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            return redirect()->route('login');
        }

        try {
            $result = $this->accessDB->deleteSupplier($id);

            if ($result) {
                $session->setFlashdata('success', 'Supplier deleted successfully');
            } else {
                $session->setFlashdata('error', 'Failed to delete supplier');
            }
        } catch (\Exception $e) {
            $session->setFlashdata('error', 'Error: ' . $e->getMessage());
        }

        return redirect()->route('supplier.index');
    }
}
