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
        Schema::table('users', function (Blueprint $table) {
            $table->string('skin_type')->nullable()->after('gender')->comment('Loại da: normal, dry, oily, combination, sensitive');
            $table->json('skin_concerns')->nullable()->after('skin_type')->comment('Các vấn đề da: acne, anti-aging, brightening, hydration');
            $table->boolean('is_sensitive')->default(false)->after('skin_concerns')->comment('Da nhạy cảm');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['skin_type', 'skin_concerns', 'is_sensitive']);
        });
    }
};
