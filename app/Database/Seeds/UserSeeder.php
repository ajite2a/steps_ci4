<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Libraries\AccessDB;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Initialize AccessDB library
        $accessDB = new AccessDB();

        // Default user data
        $defaultUsers = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => 'password123' // This will be hashed
            ],
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'john123'
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => 'jane123'
            ]
        ];

        echo "Starting user seeder...\n";

        foreach ($defaultUsers as $userData) {
            try {
                // Hash the password
                $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
                
                // Check if user already exists
                $existingUser = $accessDB->getUserByEmail($userData['email']);
                
                if ($existingUser) {
                    echo "User with email {$userData['email']} already exists. Skipping...\n";
                    continue;
                }

                // Insert user using AccessDB library
                $result = $accessDB->insertUser(
                    $userData['name'],
                    $userData['email'],
                    $hashedPassword
                );

                if ($result) {
                    echo "✓ Successfully created user: {$userData['name']} ({$userData['email']})\n";
                } else {
                    echo "✗ Failed to create user: {$userData['name']} ({$userData['email']})\n";
                }

            } catch (\Exception $e) {
                echo "✗ Error creating user {$userData['email']}: " . $e->getMessage() . "\n";
            }
        }

        echo "\nUser seeding completed!\n";
        echo "\nDefault Login Credentials:\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "Email: admin@example.com\n";
        echo "Password: password123\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    }
}
