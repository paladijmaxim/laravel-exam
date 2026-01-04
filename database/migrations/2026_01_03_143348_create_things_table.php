<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('things', function (Blueprint $table) {
            $table->id(); // id
            $table->string('name'); // name
            $table->text('description')->nullable(); // description
            $table->date('wrnt')->nullable(); // wrnt (гарантия/срок годности)
            $table->foreignId('master')->constrained('users'); // master (создатель вещи)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('things');
    }
};