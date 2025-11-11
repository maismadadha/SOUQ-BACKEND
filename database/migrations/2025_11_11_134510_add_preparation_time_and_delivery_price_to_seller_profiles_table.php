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
        Schema::table('seller_profiles', function (Blueprint $table) {
             $table->integer('preparation_days')->default(0)->after('store_cover_url')->comment('عدد أيام التحضير');
            $table->integer('preparation_hours')->default(0)->after('preparation_days')->comment('عدد ساعات التحضير');
            $table->decimal('delivery_price', 8, 2)->nullable()->after('preparation_hours')->comment('سعر التوصيل بالدينار الأردني');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seller_profiles', function (Blueprint $table) {
            //
        });
    }
};
