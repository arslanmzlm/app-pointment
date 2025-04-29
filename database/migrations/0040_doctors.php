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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('name');
            $table->string('surname');
            $table->string('full_name');
            $table->string('slug')->nullable()->unique();
            $table->string('branch');
            $table->string('avatar')->nullable();
            $table->string('title')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('resume')->nullable();
            $table->text('certificate')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('passive_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('hospital_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('doctor_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->dateTime('start_date');
            $table->dateTime('due_date');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('passive_dates');
        Schema::dropIfExists('doctors');
    }
};
