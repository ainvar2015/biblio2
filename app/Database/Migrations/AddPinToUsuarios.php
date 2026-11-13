<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPinToUsuarios extends Migration
{
    public function up()
    {
        $this->forge->addColumn('biblio2_usuarios', [
            'pin' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'password'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('biblio2_usuarios', 'pin');
    }
}