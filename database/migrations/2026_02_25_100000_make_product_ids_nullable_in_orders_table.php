<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE orders MODIFY product_id_1 INT NULL');
            DB::statement('ALTER TABLE orders MODIFY product_id_2 INT NULL');
            DB::statement('ALTER TABLE orders MODIFY product_id_3 INT NULL');
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE orders MODIFY product_id_1 INT NOT NULL');
            DB::statement('ALTER TABLE orders MODIFY product_id_2 INT NOT NULL');
            DB::statement('ALTER TABLE orders MODIFY product_id_3 INT NOT NULL');
        }
    }
};
