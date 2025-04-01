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
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained('quotations')->onDelete('cascade');
            $table->string('item_type'); // 'product' o 'service'
            $table->unsignedBigInteger('item_id')->nullable(); // ID del producto o servicio
            $table->integer('quantity')->nullable(); // Solo aplica a productos
            $table->decimal('unit_price', 10, 2);
            $table->foreignId('product_prices_id')->nullable()->constrained('product_prices')->onDelete('cascade');
            $table->unsignedBigInteger('mechanics_id')->nullable();
            $table->foreign('mechanics_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
    }
};
