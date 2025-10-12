<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `transactions` MODIFY COLUMN `mode` VARCHAR(50)");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `transactions` MODIFY COLUMN `mode` VARCHAR(10)");
    }
};
