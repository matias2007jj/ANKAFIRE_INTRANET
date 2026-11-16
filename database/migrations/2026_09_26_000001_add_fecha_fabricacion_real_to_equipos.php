<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipos', function (Blueprint $table) {
            // Guardamos la fecha completa (día 1 del mes elegido) para poder
            // recuperarla al editar. anio_fabricacion se conserva para mostrarlo rápido en la tabla.
            $table->date('fecha_fabricacion')->nullable()->after('anio_fabricacion');
        });

        // fecha_prueba_hidrostatica pasa de texto ("JUL-2025") a fecha real,
        // para poder editarla igual que las demás. Los 'S/N' existentes se pierden
        // (no eran fechas reales de todas formas) y quedan como NULL.
        Schema::table('equipos', function (Blueprint $table) {
            $table->dropColumn('fecha_prueba_hidrostatica');
        });
        Schema::table('equipos', function (Blueprint $table) {
            $table->date('fecha_prueba_hidrostatica')->nullable()->after('vencimiento_ph');
        });
    }

    public function down(): void
    {
        Schema::table('equipos', function (Blueprint $table) {
            $table->dropColumn('fecha_fabricacion');
            $table->dropColumn('fecha_prueba_hidrostatica');
        });
        Schema::table('equipos', function (Blueprint $table) {
            $table->string('fecha_prueba_hidrostatica')->nullable();
        });
    }
};
