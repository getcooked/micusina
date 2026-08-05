<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('paymongo_checkout_id')->nullable()->unique()->after('gcash_reference');
            $table->string('paymongo_payment_id')->nullable()->after('paymongo_checkout_id');
            $table->string('payment_status')->default('Pending')->after('paymongo_payment_id');
            $table->timestamp('paid_at')->nullable()->after('payment_status');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropUnique(['paymongo_checkout_id']);
            $table->dropColumn(['paymongo_checkout_id', 'paymongo_payment_id', 'payment_status', 'paid_at']);
        });
    }
};
