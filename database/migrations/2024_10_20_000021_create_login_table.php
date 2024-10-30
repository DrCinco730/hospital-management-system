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
            WHERE deleted_at IS NULL
            UNION
            SELECT id, username, password, 'admin' AS type
            FROM admins
            WHERE deleted_at IS NULL
            UNION
            SELECT id, username, password, 'nurse' AS type
            FROM nurses
            WHERE deleted_at IS NULL
            UNION
            SELECT id, username, password, 'staff' AS type
            FROM general_staff
            WHERE deleted_at IS NULL
            UNION
            SELECT id, username, password, 'doctor' AS type
            FROM doctors
            WHERE deleted_at IS NULL
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
