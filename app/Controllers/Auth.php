<?php

namespace App\Controllers;

use App\Libraries\AccessDB;

class Auth extends BaseController
{
    protected $accessDB;

    public function __construct()
    {
        // Use singleton pattern to reuse connection
        $this->accessDB = AccessDB::getInstance();
    }

    public function login()
    {
        // Display login form
        return view('auth/login');
    }

    public function authenticate()
    {
        $session = session();
        
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Validate input
        if (empty($email) || empty($password)) {
            $session->setFlashdata('error', 'Email and password are required');
            return redirect()->route('login');
        }

        // Verify credentials using AccessDB
        $user = $this->accessDB->verifyLogin($email, $password);

        if ($user) {
            // Set session data
            $sessionData = [
                'user_id' => $user['id'],
                'user_name' => $user['name'],
                'user_email' => $user['email'],
                'isLoggedIn' => true
            ];
            $session->set($sessionData);
            
            $session->setFlashdata('success', 'Login successful!');
            return redirect()->route('dashboard');
        } else {
            $session->setFlashdata('error', 'Invalid email or password');
            return redirect()->route('login');
        }
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        
        // Close database connection
        $this->accessDB->closeConnection();
        
        return redirect()->route('login')->with('success', 'You have been logged out successfully');
    }

    public function dashboard()
    {
        $session = session();
        
        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->route('login');
        }

        $data = [
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email')
        ];

        return view('auth/dashboard', $data);
    }
}
