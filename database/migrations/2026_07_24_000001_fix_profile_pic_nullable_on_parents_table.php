<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The original migration (2026_07_13_000002) specified ->nullable(),
     * but the column ended up NOT NULL in the live database anyway — every
     * new parent registration has been failing with a QueryException ever
     * since, because Authcontroller::register() never sets profile_pic.
     * Raw SQL here (not Schema::table()->change()) since that requires the
     * doctrine/dbal package, which isn't installed in this project.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE parents MODIFY profile_pic VARCHAR(255) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE parents MODIFY profile_pic VARCHAR(255) NOT NULL');
    }
};
