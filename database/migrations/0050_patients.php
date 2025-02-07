<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_id')
                ->constrained()
                ->cascadeOnUpdate();
            $table->boolean('old')->default(false);
            $table->string('name');
            $table->string('surname');
            $table->string('full_name');
            $table->string('phone')->unique();
            $table->string('email')->nullable();
            $table->unsignedTinyInteger('gender')->nullable();
            $table->date('birthday')->nullable();
            $table->boolean('notification')->default(true);
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();
        });

        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedTinyInteger('input');
            $table->string('description')->nullable();
            $table->unsignedSmallInteger('order')->nullable();
            $table->timestamps();
        });

        Schema::create('field_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('value');
            $table->timestamps();
        });

        Schema::create('patient_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('field_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('field_value_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->json('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_fields');
        Schema::dropIfExists('field_values');
        Schema::dropIfExists('fields');
        Schema::dropIfExists('patients');
    }
};
