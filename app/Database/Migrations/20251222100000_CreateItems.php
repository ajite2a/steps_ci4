<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use App\Libraries\AccessDB;

class CreateItems extends Migration
{
    public function up()
    {
        // Use AccessDB library to create the table in MS Access
        $access = AccessDB::getInstance();

        $fields = [
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'product_code' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'product_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'item_date' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'supplier_id' => [
                'type' => 'INT',
                'null' => true,
            ],
            'color_id' => [
                'type' => 'INT',
                'null' => true,
            ],
            'article' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'product_group' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'brand' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'heels' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'tags' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'category' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'purchase_rate' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'gst' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'mrp' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'purchase_code' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'size_from' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'img_code' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ];

        $access->createTable('items', $fields);
    }

    public function down()
    {
        $access = AccessDB::getInstance();
        $access->dropTable('items');
    }
}

