<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::create('supports', function (Blueprint $table) {
        $table->id();
        $table->string('title');          // Tiêu đề yêu cầu hỗ trợ
        $table->text('message');          // Nội dung chi tiết (thay vì content)
        $table->string('status')->default('pending');  // pending/processing/completed/cancelled
        $table->unsignedBigInteger('created_by')->nullable();      // user id tạo (có thể null cho guest)
        $table->string('email');  // Email của người gửi (không nullable)
        $table->string('name');  // Họ tên (không nullable)
        $table->timestamps();

        $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supports');
    }
};
