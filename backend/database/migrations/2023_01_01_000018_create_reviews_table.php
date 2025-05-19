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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('rating')->comment('Rating from 1-5');
            $table->text('comment')->nullable();
            $table->string('title')->nullable();
            $table->boolean('verified_purchase')->default(false);
            $table->boolean('approved')->default(false);
            $table->boolean('featured')->default(false);
            $table->timestamps();
            
            // Ensure a user can only review a product once
            $table->unique(['product_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
