<?php

namespace NotesApi\Database\Migrations;

use NotesApi\Database\Migration;

return new class extends Migration
{
    public function up()
    {
        $this->createTable('user_notes', [
            'id INT AUTO_INCREMENT PRIMARY KEY',
            'user_id INT NOT NULL',
            'note_id INT NOT NULL',
            'FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE',
            'FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE',
            'UNIQUE KEY (user_id, note_id)',
        ]);
    }

    public function down()
    {
        $this->dropTable('user_notes');
    }
};
