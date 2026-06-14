<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use App\Libraries\AccessDB;

class CreateChangeLog extends Migration
{
    public function up()
    {
        $access = AccessDB::getInstance();

        $fields = [
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'table_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'record_id' => [
                'type' => 'INT',
                'null' => false,
            ],
            'change_type' => [
                'type' => 'VARCHAR',
                'constraint' => 10, // INSERT, UPDATE, DELETE
                'null' => false,
            ],
            'synced' => [
                'type' => 'INT', // 0 = pending, 1 = synced
                'null' => false,
                'default' => 0,
            ],
            'changed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ];

        $access->createTable('change_log', $fields);
    }

    public function down()
    {
        $access = AccessDB::getInstance();
        $access->dropTable('change_log');
    }
}
