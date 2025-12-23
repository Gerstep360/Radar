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
        Schema::create('media', function (Blueprint $table) {
                $table->id();
                // Si borras el reporte, se borran las fotos (cascade)
                $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
                $table->string('file_path'); // Guardamos solo la ruta: 'evidencia/foto1.jpg'
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
