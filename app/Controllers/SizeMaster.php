<?php

namespace App\Controllers;

use App\Libraries\AccessDB;

class SizeMaster extends BaseController
{
    protected $accessDB;

    public function __construct()
    {
        $this->accessDB = AccessDB::getInstance();
    }

    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->route('login');
        }

        $masters = $this->accessDB->getAllSizeMasters();

        $data = [
            'size_masters' => $masters,
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email')
        ];

        return view('size_master/index', $data);
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

        return view('size_master/form', $data);
    }

    public function store()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->route('login');
        }

        $master_name = $this->request->getPost('master_name');
        $size_values = $this->request->getPost('size_value');
        $new_sizes = $this->request->getPost('new_size');

        if (empty($master_name)) {
            $session->setFlashdata('error', 'Master name is required');
            return redirect()->route('sizemaster.create');
        }

        // duplicate check
        if ($this->accessDB->sizeMasterNameExists($master_name)) {
            $session->setFlashdata('error', 'Master name already exists');
            return redirect()->route('sizemaster.create');
        }

        // Validate and prepare sizes array
        $sizes = [];
        if (!empty($size_values) && is_array($size_values)) {
            foreach ($size_values as $key => $size_value) {
                $size_value = trim($size_value);
                if (!empty($size_value)) {
                    $sizes[] = [
                        'size_value' => $size_value,
                        'new_size' => isset($new_sizes[$key]) ? trim($new_sizes[$key]) : ''
                    ];
                }
            }
        }

        if (empty($sizes)) {
            $session->setFlashdata('error', 'At least one size is required');
            return redirect()->route('sizemaster.create');
        }

        try {
            $this->accessDB->createSizeMaster($master_name, $sizes);
            $session->setFlashdata('success', 'Size master created');
            return redirect()->route('sizemaster.index');
        } catch (\Exception $e) {
            $session->setFlashdata('error', 'Error: ' . $e->getMessage());
            return redirect()->route('sizemaster.create');
        }
    }

    public function edit($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->route('login');
        }

        $master = $this->accessDB->getSizeMasterById($id);
        if (!$master) {
            $session->setFlashdata('error', 'Size master not found');
            return redirect()->route('sizemaster.index');
        }

        $data = [
            'size_master' => $master,
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email')
        ];

        return view('size_master/form', $data);
    }

    public function update($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->route('login');
        }

        $master_name = $this->request->getPost('master_name');
        $size_values = $this->request->getPost('size_value');
        $new_sizes = $this->request->getPost('new_size');

        if (empty($master_name)) {
            $session->setFlashdata('error', 'Master name is required');
            return redirect()->route('sizemaster.edit', $id);
        }

        if ($this->accessDB->sizeMasterNameExists($master_name, $id)) {
            $session->setFlashdata('error', 'Master name already exists');
            return redirect()->route('sizemaster.edit', $id);
        }

        // Validate and prepare sizes array
        $sizes = [];
        if (!empty($size_values) && is_array($size_values)) {
            foreach ($size_values as $key => $size_value) {
                $size_value = trim($size_value);
                if (!empty($size_value)) {
                    $sizes[] = [
                        'size_value' => $size_value,
                        'new_size' => isset($new_sizes[$key]) ? trim($new_sizes[$key]) : ''
                    ];
                }
            }
        }

        if (empty($sizes)) {
            $session->setFlashdata('error', 'At least one size is required');
            return redirect()->route('sizemaster.edit', $id);
        }

        try {
            $this->accessDB->updateSizeMaster($id, $master_name, $sizes);
            $session->setFlashdata('success', 'Size master updated');
            return redirect()->route('sizemaster.index');
        } catch (\Exception $e) {
            $session->setFlashdata('error', 'Error: ' . $e->getMessage());
            return redirect()->route('sizemaster.edit', $id);
        }
    }

    public function delete($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->route('login');
        }

        try {
            $this->accessDB->deleteSizeMaster($id);
            $session->setFlashdata('success', 'Size master deleted');
        } catch (\Exception $e) {
            $session->setFlashdata('error', 'Error: ' . $e->getMessage());
        }

        return redirect()->route('sizemaster.index');
    }
}
