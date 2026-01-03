<?php

namespace App\Controllers;

use App\Libraries\AccessDB;

class Color extends BaseController
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

        $colors = $this->accessDB->getAllColors();

        $data = [
            'colors' => $colors,
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email')
        ];

        return view('color/index', $data);
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

        return view('color/form', $data);
    }

    public function store()
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            return redirect()->route('login');
        }

        $color_name = $this->request->getPost('color_name');

        // Validate input
        if (empty($color_name)) {
            $session->setFlashdata('error', 'Color name is required');
            return redirect()->route('color.create');
        }

        // Check for duplicate color name
        if ($this->accessDB->colorNameExists($color_name)) {
            $session->setFlashdata('error', 'Color name already exists');
            return redirect()->route('color.create');
        }

        try {
            $result = $this->accessDB->createColor($color_name);

            if ($result) {
                $session->setFlashdata('success', 'Color created successfully');
                return redirect()->route('color.index');
            } else {
                $session->setFlashdata('error', 'Failed to create color');
                return redirect()->route('color.create');
            }
        } catch (\Exception $e) {
            $session->setFlashdata('error', 'Error: ' . $e->getMessage());
            return redirect()->route('color.create');
        }
    }

    public function edit($id)
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            return redirect()->route('login');
        }

        $color = $this->accessDB->getColorById($id);

        if (!$color) {
            $session->setFlashdata('error', 'Color not found');
            return redirect()->route('color.index');
        }

        $data = [
            'color' => $color,
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email')
        ];

        return view('color/form', $data);
    }

    public function update($id)
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            return redirect()->route('login');
        }

        $color_name = $this->request->getPost('color_name');

        if (empty($color_name)) {
            $session->setFlashdata('error', 'Color name is required');
            return redirect()->route('color.edit', $id);
        }

        // Check for duplicate color name (excluding current record)
        if ($this->accessDB->colorNameExists($color_name, $id)) {
            $session->setFlashdata('error', 'Color name already exists');
            return redirect()->route('color.edit', $id);
        }

        try {
            $result = $this->accessDB->updateColor($id, $color_name);

            if ($result) {
                $session->setFlashdata('success', 'Color updated successfully');
                return redirect()->route('color.index');
            } else {
                $session->setFlashdata('error', 'Failed to update color');
                return redirect()->route('color.edit', $id);
            }
        } catch (\Exception $e) {
            $session->setFlashdata('error', 'Error: ' . $e->getMessage());
            return redirect()->route('color.edit', $id);
        }
    }

    public function delete($id)
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            return redirect()->route('login');
        }

        try {
            $result = $this->accessDB->deleteColor($id);

            if ($result) {
                $session->setFlashdata('success', 'Color deleted successfully');
            } else {
                $session->setFlashdata('error', 'Failed to delete color');
            }
        } catch (\Exception $e) {
            $session->setFlashdata('error', 'Error: ' . $e->getMessage());
        }

        return redirect()->route('color.index');
    }
}
