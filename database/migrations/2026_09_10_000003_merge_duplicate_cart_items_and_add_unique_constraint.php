<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicateKeys = DB::table('carts')
            ->select('userid', 'food_id')
            ->whereNotNull('userid')
            ->whereNotNull('food_id')
            ->groupBy('userid', 'food_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicateKeys as $key) {
            $items = DB::table('carts')
                ->where('userid', $key->userid)
                ->where('food_id', $key->food_id)
                ->orderBy('id')
                ->get();
            $keeper = $items->first();

            if (! $keeper) {
                continue;
            }

            $quantity = $items->sum(fn ($item) => max(0, (int) $item->quantity));
            $price = $items->sum(fn ($item) => (float) preg_replace('/[^0-9.\-]/', '', (string) $item->price));
            $formattedPrice = rtrim(rtrim(number_format($price, 2, '.', ''), '0'), '.');

            DB::table('carts')->where('id', $keeper->id)->update([
                'quantity' => (string) $quantity,
                'price' => $formattedPrice,
                'updated_at' => now(),
            ]);

            DB::table('carts')
                ->whereIn('id', $items->pluck('id')->reject(fn ($id) => (int) $id === (int) $keeper->id))
                ->delete();
        }

        Schema::table('carts', function (Blueprint $table) {
            $table->unique(['userid', 'food_id']);
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropUnique(['userid', 'food_id']);
        });
    }
};
