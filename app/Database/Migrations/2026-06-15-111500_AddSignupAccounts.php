<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSignupAccounts extends Migration
{
    public function up()
    {
        $this->forge->addColumn('school_accounts', [
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 190,
                'null' => true,
                'after' => 'school_name',
            ],
        ]);

        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 190,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 190,
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('public_accounts', true);
    }

    public function down()
    {
        $this->forge->dropTable('public_accounts', true);
        $this->forge->dropColumn('school_accounts', 'email');
    }
}
