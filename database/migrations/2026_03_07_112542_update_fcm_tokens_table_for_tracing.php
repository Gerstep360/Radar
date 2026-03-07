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
        Schema::table('fcm_tokens', function (Blueprint $table) {
            // Aseguramos que device_id no sea nulo para el indice unico
            $table->string('device_id')->nullable(false)->change(); 
            $table->unique(['user_id', 'device_id']);
            $table->timestamp('last_seen_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fcm_tokens', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'device_id']);
            $table->dropColumn('last_seen_at');
            $table->string('device_id')->nullable()->change();
        });
    }
};
