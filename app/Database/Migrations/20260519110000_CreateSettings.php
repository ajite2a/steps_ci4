<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use App\Libraries\AccessDB;

class CreateSettings extends Migration
{
    public function up()
    {
        $access = AccessDB::getInstance();

        $fields = [
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'setting_key' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'setting_value' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
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

        $access->createTable('settings', $fields);

        // Seed default settings
        $access->upsertSetting('item_scan_type', 'barcode');
    }

    public function down()
    {
        $access = AccessDB::getInstance();
        $access->dropTable('settings');
    }
}
