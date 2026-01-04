<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archived_things', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_id')->nullable()->comment('ID оригинальной вещи');
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('wrnt')->nullable();
            
            // Информация о владельце
            $table->string('owner_name');
            $table->string('owner_email');
            
            // Информация о последнем пользователе
            $table->string('last_user_name')->nullable();
            $table->string('last_user_email')->nullable();
            
            // Информация о месте хранения
            $table->string('place_name')->nullable();
            $table->text('place_description')->nullable();
            
            // Флаг восстановления
            $table->boolean('restored')->default(false);
            $table->timestamp('restored_at')->nullable();
            $table->unsignedBigInteger('restored_by')->nullable()->comment('Кто восстановил');
            $table->string('restored_by_name')->nullable();
            
            // Количество и единица измерения на момент архивации
            $table->integer('amount')->default(1);
            $table->string('unit_name')->nullable();
            $table->string('unit_abbreviation')->nullable();
            
            // Мета-данные
            $table->timestamp('deleted_at')->useCurrent();
            $table->json('metadata')->nullable()->comment('Дополнительные данные');
            
            $table->timestamps();
            
            // Индексы
            $table->index('original_id');
            $table->index('restored');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archived_things');
    }
};