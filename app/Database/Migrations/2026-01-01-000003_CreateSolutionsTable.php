<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSolutionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'sub_category_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
            ],
            'symptoms' => [
                'type' => 'TEXT',
            ],
            'root_cause' => [
                'type' => 'TEXT',
            ],
            'steps' => [
                'type' => 'TEXT',
            ],
            'attachment' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'is_pinned' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('sub_category_id', 'sub_categories', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('solutions', true);
    }

    public function down()
    {
        $this->forge->dropTable('solutions', true);
    }
}
