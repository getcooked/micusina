<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('food', function (Blueprint $table) {
            $table->timestamp('low_stock_email_sent_at')->nullable()->after('low_stock_notified_at');
            $table->timestamp('low_stock_sms_sent_at')->nullable()->after('low_stock_email_sent_at');
        });

        DB::table('food')
            ->where('stock', '<=', 5)
            ->update(['low_stock_notified_at' => null]);
    }

    public function down(): void
    {
        Schema::table('food', function (Blueprint $table) {
            $table->dropColumn(['low_stock_email_sent_at', 'low_stock_sms_sent_at']);
        });
    }
};
