<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('places', function (Blueprint $table) {
            $table->id(); // id
            $table->string('name'); // name
            $table->text('description')->nullable(); // description
            $table->boolean('repair')->default(false); // repair (флаг ремонт/мойка)
            $table->boolean('work')->default(false); // work (находится в работе)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('places');
    }
};