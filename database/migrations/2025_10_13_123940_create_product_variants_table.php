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
        Schema::create('product_variants', function (Blueprint $table) {
          $table->id();
          $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
          $table->string('sku', 100)->nullable()->unique();
          $table->integer('quantity')->default(0);
          $table->string('variant_key', 255);  // مثل "size:M|color:red|"
          $table->unique(['product_id','variant_key']); // نفس ux_variant_product_key
          $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
