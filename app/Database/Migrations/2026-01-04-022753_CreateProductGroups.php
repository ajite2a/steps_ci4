<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use App\Libraries\AccessDB;

class CreateProductGroups extends Migration
{
    public function up()
    {
        $accessDB = AccessDB::getInstance();

        $fields = [
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'group_name' => [
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

        $accessDB->createTable('product_groups', $fields);
    }

    public function down()
    {
        $accessDB = AccessDB::getInstance();
        $accessDB->dropTable('product_groups');
    }
}
