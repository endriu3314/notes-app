<?php

namespace NotesApi\Database\Migrations;

use NotesApi\Database\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $this->addColumn('users', 'session_token', 'VARCHAR(64) NULL');
    }

    public function down(): void
    {
        $this->dropColumn('users', 'session_token');
    }
};
