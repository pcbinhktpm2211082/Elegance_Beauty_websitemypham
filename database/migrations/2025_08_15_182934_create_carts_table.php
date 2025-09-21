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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable(); // Cho khách hàng chưa đăng nhập
            $table->unsignedBigInteger('user_id')->nullable(); // Cho khách hàng đã đăng nhập
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variant_id')->nullable(); // Nếu sản phẩm có biến thể
            $table->integer('quantity');
            $table->decimal('price', 10, 2); // Giá tại thời điểm thêm vào giỏ
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            
            // Đảm bảo không có duplicate items
            $table->unique(['session_id', 'product_id', 'variant_id']);
            $table->unique(['user_id', 'product_id', 'variant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
