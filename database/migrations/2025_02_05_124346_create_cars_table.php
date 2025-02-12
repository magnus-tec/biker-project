<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('drives_id')->nullable()->constrained('drives')->onDelete('cascade');
            $table->string('codigo', 20)->unique()->nullable();
            $table->string('placa', 20)->unique()->nullable();
            $table->string('marca', 50);
            $table->string('modelo', 50);
            $table->string('anio')->nullable();
            $table->string('condicion', 20)->nullable();
            $table->string('nro_chasis', 20)->nullable();
            $table->date('fecha_soat')->nullable();
            $table->date('fecha_seguro')->nullable();
            $table->string('color', 20)->nullable();
            $table->unsignedBigInteger('user_register')->nullable();
            $table->foreign('user_register')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('user_update')->nullable();
            $table->foreign('user_update')->references('id')->on('users')->onDelete('cascade');
            $table->boolean('status')->default(1);
            $table->timestamp('fecha_registro')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('fecha_actualizacion')->default(DB::raw('CURRENT_TIMESTAMP'))->onUpdate(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
