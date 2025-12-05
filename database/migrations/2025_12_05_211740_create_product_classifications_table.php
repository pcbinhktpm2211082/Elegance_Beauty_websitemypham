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
        Schema::create('product_classifications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['skin_type', 'skin_concern']); // Loại da hoặc Vấn đề da
            $table->timestamps();
        });

        // Tạo pivot table
        Schema::create('product_product_classification', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_classification_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['product_id', 'product_classification_id'], 'product_class_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_product_classification');
        Schema::dropIfExists('product_classifications');
    }
};
