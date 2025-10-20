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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('store_id')->constrained('users')->onDelete('cascade');
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->decimal('delivery_fee', 12, 2)->default(0.00);
            $table->decimal('discount_total', 12, 2)->default(0.00);
            $table->decimal('total_price', 12, 2)->virtualAs('subtotal + delivery_fee - discount_total'); // MySQL 5.7+
            $table->integer('items_count')->default(0);
            $table->text('note')->nullable();
            $table->enum('status', ['ON_CART','CONFIRMED','PREPARING','OUT_FOR_DELIVERY','DELIVERED','CANCELLED'])
                  ->default('ON_CART');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
