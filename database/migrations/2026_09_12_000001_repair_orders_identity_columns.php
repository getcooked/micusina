<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('orders', 'user_id')) {
            Schema::table('orders', function (Blueprint $table): void {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
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
        // This is a production-schema repair migration; do not remove live order identity data on rollback.
    }
};
