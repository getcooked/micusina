<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Repair installations where the earlier order-identity migration was
     * recorded but its schema changes were not applied.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('orders', 'user_id')) {
            Schema::table('orders', function (Blueprint $table): void {
                $table->foreignId('user_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('orders', 'checkout_group_id')) {
            $after = Schema::hasColumn('orders', 'user_id') ? 'user_id' : 'id';

            Schema::table('orders', function (Blueprint $table) use ($after): void {
                $table->uuid('checkout_group_id')->nullable()->after($after)->index();
            });
        }
    }

    public function down(): void
    {
        // Do not remove identity data from an existing production order table.
    }
};
