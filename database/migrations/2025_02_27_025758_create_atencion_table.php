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
        Schema::create('atencion', function (Blueprint $table) {
            $table->id();
            $table->integer('id_cita');
            $table->integer('id_servicio');
            $table->integer('id_empleado');
            $table->dateTime('fecha_hora');
            $table->decimal('costo_final', 8, 2);
            $table->unique(['id_cita', 'id_servicio', 'id_empleado']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atencion');
    }
};
