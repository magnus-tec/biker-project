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
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade'); // Relación con la venta
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // Producto vendido
            $table->integer('quantity'); // Cantidad vendida
            $table->decimal('unit_price', 10, 2); // Precio unitario en el momento de la venta
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
