<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('broadcasts', function (Blueprint $table) {
            $table->string('image_url')->nullable()->after('body');
            $table->boolean('is_popup')->default(false)->after('image_url');
            $table->integer('auto_close_seconds')->default(10)->after('is_popup');
        });
    }

    public function down(): void
    {
        Schema::table('broadcasts', function (Blueprint $table) {
            $table->dropColumn(['image_url', 'is_popup', 'auto_close_seconds']);
        });
    }
};
