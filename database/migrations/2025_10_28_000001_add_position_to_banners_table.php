<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('banners', 'position')) {
            Schema::table('banners', function (Blueprint $table) {
                $table->enum('position', ['left', 'right_top', 'right_bottom'])
                    ->default('left')
                    ->after('order');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('banners', 'position')) {
            Schema::table('banners', function (Blueprint $table) {
                $table->dropColumn('position');
            });
        }
    }
};


