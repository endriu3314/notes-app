<?php

namespace NotesApi\Database\Migrations;

use NotesApi\Database\Migration;

return new class extends Migration
{
    public function up()
    {
        $this->createTable('notes', [
            'id INT AUTO_INCREMENT PRIMARY KEY',
            'user_id INT NOT NULL',
            'title VARCHAR(255) NOT NULL',
            'content TEXT NOT NULL',
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            'FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE',
        ]);
    }

    public function down()
    {
        $this->dropTable('notes');
    }
};
