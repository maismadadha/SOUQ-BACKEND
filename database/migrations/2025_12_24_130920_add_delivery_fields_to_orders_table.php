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
         Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('delivery_id')->nullable()->after('store_id');
            $table->timestamp('picked_at')->nullable()->after('delivery_id');
            $table->timestamp('delivered_at')->nullable()->after('picked_at');

            $table->foreign('delivery_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['delivery_id']);
            $table->dropColumn(['delivery_id', 'picked_at', 'delivered_at']);
        });
    }
};
