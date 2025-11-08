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
        Schema::create('order_items', function (Blueprint $table) {
              $table->id();

            // الربط بالطلب
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');

            // الربط بالمنتج
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

            // الكمية والسعر
            $table->integer('quantity')->default(1);
            $table->decimal('price', 12, 2)->default(0.00);

            // الخصم لو فيه
            $table->decimal('discount', 12, 2)->default(0.00);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
