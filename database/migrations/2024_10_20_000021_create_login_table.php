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
            CREATE VIEW login_view AS
            SELECT id, username, password, 'user' AS type
            FROM users
            UNION
            SELECT id, username, password, 'admin' AS type
            FROM admins
            UNION
            SELECT id, username, password, 'nurse' AS type
            FROM nurses
            UNION
            SELECT id, username, password, 'staff' AS type
            FROM general_staff
             UNION
            SELECT id, username, password, 'doctor' AS type
            FROM doctors
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS login_view");
    }
};
