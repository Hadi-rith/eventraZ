<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProgramParentId extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('parent_id', 'programs')) {
            $this->forge->addColumn('programs', [
                'parent_id' => [
                    'type'     => 'INT',
                    'unsigned' => true,
                    'null'     => true,
                    'after'    => 'id',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('parent_id', 'programs')) {
            $this->forge->dropColumn('programs', 'parent_id');
        }
    }
}
