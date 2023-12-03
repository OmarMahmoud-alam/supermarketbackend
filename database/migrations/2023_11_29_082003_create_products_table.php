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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('category_id');
            $table->string('brand');
            $table->decimal('price', 10, 2);
            $table->text('description')->nullable();
            $table->bigInteger('quantity');
            $table->string('image');

            $table->boolean('availability')->default(true);
            $table->boolean('isvisible')->default(true);
            $table->enum('type',['deliverable','inlocation'])->default('deliverable');

            $table->timestamps();

            // Foreign key relationship
            $table->foreign('category_id')->references('id')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
