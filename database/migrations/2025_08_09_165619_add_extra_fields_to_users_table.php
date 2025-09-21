<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('users', function (Blueprint $table) {
        if (!Schema::hasColumn('users', 'phone')) {
            $table->string('phone', 20)->nullable()->after('email');
        }
        if (!Schema::hasColumn('users', 'gender')) {
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
        }
        if (!Schema::hasColumn('users', 'dob')) {
            $table->date('dob')->nullable();
        }
        if (!Schema::hasColumn('users', 'avatar')) {
            $table->string('avatar')->nullable();
        }
        if (!Schema::hasColumn('users', 'address')) {
            $table->string('address')->nullable();
        }
        // Không thêm role và status vì đã có
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['phone', 'gender', 'dob', 'avatar', 'address']);
    });
}

};
