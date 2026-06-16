<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProgramDates extends Migration
{
    public function up()
    {
        $fields = [
            'start_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'program_name',
            ],
            'end_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'start_date',
            ],
        ];

        $this->forge->addColumn('programs', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('programs', ['start_date', 'end_date']);
    }
}
