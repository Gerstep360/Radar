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
        Schema::create('report_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
                // Relación manual a users porque el campo se llama admin_id
                $table->foreignId('admin_id')->constrained('users'); 
                
                $table->string('comment')->nullable(); // Ej: "Cuadrilla enviada"
                $table->timestamp('created_at')->useCurrent(); // Solo nos importa cuándo pasó
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_logs');
    }
};
