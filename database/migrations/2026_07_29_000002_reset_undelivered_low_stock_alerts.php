<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('food')
            ->where('stock', '<=', 5)
            ->update(['low_stock_notified_at' => null]);
    }

    public function down(): void
    {
        // Delivery timestamps cannot be reconstructed.
    }
};
