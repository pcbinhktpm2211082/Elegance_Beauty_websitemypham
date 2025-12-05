<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('product_type')->nullable()->after('category_id')->comment('Loại sản phẩm: Lip Balm, Body Lotion, Makeup, Skincare, etc.');
            $table->json('sensitive_flags')->nullable()->after('description')->comment('Các tag an toàn: Alcohol-Free, Fragrance-Free, etc.');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['product_type', 'sensitive_flags']);
        });
    }
};
