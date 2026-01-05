<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thing_descriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thing_id')->constrained()->onDelete('cascade');
            $table->text('description');
            $table->boolean('is_current')->default(false);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thing_descriptions');
    }
};