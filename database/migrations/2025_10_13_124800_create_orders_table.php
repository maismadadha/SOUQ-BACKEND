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

            // الزبون والمتجر
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('store_id')->constrained('users')->onDelete('cascade');

            // عنوان الطلب
            $table->foreignId('address_id')->nullable()->constrained('addresses')->onDelete('set null');

            // الأسعار
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->decimal('delivery_fee', 12, 2)->default(0.00);
            $table->decimal('discount_total', 12, 2)->default(0.00);
            $table->decimal('total_price', 12, 2)->default(0.00); // نخزنها فعليًا

            // عدد المنتجات والملاحظات
            $table->integer('items_count')->default(0);
            $table->text('note')->nullable();

            // حالة الطلب
            $table->enum('status', [
                'ON_CART',
                'CONFIRMED',
                'PREPARING',
                'OUT_FOR_DELIVERY',
                'DELIVERED',
                'CANCELLED'
            ])->default('ON_CART');

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
