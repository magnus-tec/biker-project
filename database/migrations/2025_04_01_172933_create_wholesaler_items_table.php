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
        Schema::create('wholesaler_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wholesaler_id')->constrained('wholesalers')->onDelete('cascade');
            $table->string('item_type');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->integer('quantity')->nullable();
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
        Schema::dropIfExists('wholesaler_items');
    }
};
