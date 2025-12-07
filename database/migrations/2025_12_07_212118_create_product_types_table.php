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
        Schema::create('product_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Tên loại sản phẩm: Exfoliator, Lip Balm, Body Lotion, Makeup, etc.');
            $table->boolean('requires_skin_type_filter')->default(true)->comment('Có cần áp dụng bộ lọc loại da (Da Dầu/Khô) hay không');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_types');
    }
};
