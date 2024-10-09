<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            CREATE VIEW user_admin_view AS
            SELECT id, username, password, 'user' AS type
            FROM users
            UNION
            SELECT id, username, password, 'admin' AS type
            FROM admins
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS user_admin_view");
    }
};
