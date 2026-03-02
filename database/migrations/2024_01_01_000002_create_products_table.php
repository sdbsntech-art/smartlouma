<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category', ['Légumes', 'Fruits', 'Tubercules', 'Herbes', 'Épices']);
            $table->unsignedInteger('quantity')->default(0);   // kg disponibles
            $table->unsignedInteger('price');                   // FCFA/kg
            $table->string('zone');
            $table->date('harvest_date')->nullable();
            $table->text('description')->nullable();
            $table->text('image')->nullable();                  // URL ou base64
            $table->decimal('rating', 2, 1)->default(0);
            $table->boolean('available')->default(true);
            $table->unsignedInteger('sold_qty')->default(0);
            $table->foreignId('producer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
