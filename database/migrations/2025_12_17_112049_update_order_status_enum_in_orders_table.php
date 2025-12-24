<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE orders
            MODIFY status ENUM(
                'ON_CART',
                'CONFIRMED',
                'PREPARING',
                'READY_FOR_PICKUP',
                'OUT_FOR_DELIVERY',
                'CASH_COLLECTED',
                'DELIVERED',
                'CANCELLED'
            ) NOT NULL DEFAULT 'ON_CART'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {  DB::statement("
            ALTER TABLE orders
            MODIFY status ENUM(
                'ON_CART',
                'CONFIRMED',
                'PREPARING',
                'OUT_FOR_DELIVERY',
                'DELIVERED',
                'CANCELLED'
            ) NOT NULL DEFAULT 'ON_CART'
        ");
    }
};
