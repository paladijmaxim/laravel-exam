<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uses', function (Blueprint $table) {
            // СТРОГО как в задании: thing_id, place_id, user_id, amount
            $table->foreignId('thing_id')->constrained('things')->onDelete('cascade');
            $table->foreignId('place_id')->constrained('places')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('amount')->default(1); // количество
            
            // Составной первичный ключ
            $table->primary(['thing_id', 'place_id', 'user_id']);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uses');
    }
};