<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use App\Libraries\AccessDB;

class CreateSizes extends Migration
{
    public function up()
    {
        $accessDB = AccessDB::getInstance();

        $fields = [
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'size_master_id' => [
                'type' => 'INT',
            ],
            'size_value' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'new_size' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
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

        $accessDB->createTable('sizes', $fields);
    }

    public function down()
    {
        $accessDB = AccessDB::getInstance();
        $accessDB->dropTable('sizes');
    }
}
