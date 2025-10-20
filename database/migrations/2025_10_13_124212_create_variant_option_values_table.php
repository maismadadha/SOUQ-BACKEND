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
        Schema::create('variant_option_values', function (Blueprint $table) {
           $table->foreignId('variant_id')->constrained('product_variants')->onDelete('cascade');
           $table->foreignId('product_option_id')->constrained('product_options')->onDelete('cascade');
           $table->foreignId('product_option_value_id')->constrained('product_option_values')->onDelete('cascade');
           $table->primary(['variant_id','product_option_id']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variant_option_values');
    }
};
