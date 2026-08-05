<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
            $table->decimal('reservation_price', 10, 2)->default(0)->after('time');
            $table->decimal('deposit_amount', 10, 2)->default(0)->after('reservation_price');
            $table->string('payment_method')->default('GCash')->after('deposit_amount');
            $table->string('gcash_reference')->nullable()->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn([
                'name',
                'reservation_price',
                'deposit_amount',
                'payment_method',
                'gcash_reference',
            ]);
        });
    }
};
