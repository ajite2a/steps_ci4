<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use App\Libraries\AccessDB;

class AddDisplayPriceToItems extends Migration
{
    public function up()
    {
        $access = AccessDB::getInstance();
        try {
            $access->addColumn('items', 'display_price', 'TEXT NULL');
        } catch (\Exception $e) {
            // Column may already exist
        }
    }

    public function down()
    {
        // MS Access does not support DROP COLUMN
    }
}
