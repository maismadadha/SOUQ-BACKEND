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
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id();

            // الربط بالطلب
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');

            // الربط بالمندوب
            $table->foreignId('delivery_id')->constrained('users')->onDelete('cascade');

            // وقت الاستلام والتسليم
            $table->timestamp('picked_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_orders');
    }
};
