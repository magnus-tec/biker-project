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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->nullable();
            $table->string('customer_dni')->nullable();
            $table->string('customer_names_surnames')->nullable();
            $table->decimal('total_price', 10, 2);
            $table->decimal('igv', 10, 2);
            $table->string('observation')->nullable();
            $table->integer('nro_dias')->nullable();
            $table->string('customer_address')->nullable();
            $table->string('status')->default('1');
            $table->string('status_sale')->default('0');
            $table->unsignedBigInteger('document_type_id')->nullable();
            $table->foreign('document_type_id')->references('id')->on('document_types')->onDelete('cascade');
            $table->foreignId('districts_id')->nullable()->constrained('districts')->onDelete('cascade');
            $table->foreignId('payments_id')->nullable()->constrained('payments')->onDelete('cascade');
            $table->foreignId('companies_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->unsignedBigInteger('mechanics_id')->nullable();
            $table->foreign('mechanics_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('user_register')->nullable();
            $table->foreign('user_register')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('user_update')->nullable();
            $table->foreign('user_update')->references('id')->on('users')->onDelete('cascade');
            $table->timestamp('fecha_registro')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->date('fecha_vencimiento')->nullable();
            $table->timestamp('fecha_actualizacion')->default(DB::raw('CURRENT_TIMESTAMP'))->onUpdate(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
