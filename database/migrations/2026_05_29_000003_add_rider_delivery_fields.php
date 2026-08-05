<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('rider_available')->default(true)->after('staff_role');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('rider_id')->nullable()->after('payment_reference')->constrained('users')->nullOnDelete();
            $table->foreignId('confirmed_by')->nullable()->after('rider_id')->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable()->after('confirmed_by');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('rider_id');
            $table->dropConstrainedForeignId('confirmed_by');
            $table->dropColumn('confirmed_at');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('rider_available');
        });
    }
};
