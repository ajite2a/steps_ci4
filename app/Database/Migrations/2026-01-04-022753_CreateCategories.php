<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use App\Libraries\AccessDB;

class CreateCategories extends Migration
{
    public function up()
    {
        $accessDB = AccessDB::getInstance();

        $fields = [
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'category_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
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

        $accessDB->createTable('categories', $fields);
    }

    public function down()
    {
        $accessDB = AccessDB::getInstance();
        $accessDB->dropTable('categories');
    }
}
