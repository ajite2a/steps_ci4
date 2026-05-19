<?php

namespace App\Controllers;

use App\Libraries\AccessDB;

class Setting extends BaseController
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

        $data = [
            'settings'   => $this->accessDB->getAllSettings(),
            'user_name'  => $session->get('user_name'),
            'user_email' => $session->get('user_email'),
            'pageTitle'  => 'Settings',
        ];

        return view('setting/index', $data);
    }

    public function store()
    {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            return redirect()->route('login');
        }

        $key   = trim($this->request->getPost('setting_key'));
        $value = trim($this->request->getPost('setting_value'));

        if (empty($key)) {
            $session->setFlashdata('error', 'Setting key is required');
            return redirect()->route('setting.index');
        }

        try {
            $this->accessDB->upsertSetting($key, $value);
            $session->setFlashdata('success', 'Setting saved successfully');
        } catch (\Exception $e) {
            $session->setFlashdata('error', 'Error: ' . $e->getMessage());
        }

        return redirect()->route('setting.index');
    }

    public function update()
    {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            return redirect()->route('login');
        }

        $keys   = $this->request->getPost('setting_key');
        $values = $this->request->getPost('setting_value');

        if (!is_array($keys)) {
            $session->setFlashdata('error', 'Invalid data');
            return redirect()->route('setting.index');
        }

        try {
            foreach ($keys as $i => $key) {
                $key = trim($key);
                if (!empty($key)) {
                    $this->accessDB->upsertSetting($key, trim($values[$i] ?? ''));
                }
            }
            $session->setFlashdata('success', 'Settings updated successfully');
        } catch (\Exception $e) {
            $session->setFlashdata('error', 'Error: ' . $e->getMessage());
        }

        return redirect()->route('setting.index');
    }

    public function delete($id)
    {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            return redirect()->route('login');
        }

        try {
            $result = $this->accessDB->deleteSetting($id);
            if ($result) {
                $session->setFlashdata('success', 'Setting deleted successfully');
            } else {
                $session->setFlashdata('error', 'Failed to delete setting');
            }
        } catch (\Exception $e) {
            $session->setFlashdata('error', 'Error: ' . $e->getMessage());
        }

        return redirect()->route('setting.index');
    }
}
