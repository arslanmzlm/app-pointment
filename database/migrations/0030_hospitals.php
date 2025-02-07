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
        Schema::create('hospitals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_id')
                ->constrained()
                ->cascadeOnUpdate();
            $table->string('name');
            $table->unsignedTinyInteger('start_work');
            $table->unsignedTinyInteger('end_work');
            $table->unsignedTinyInteger('duration');
            $table->string('title')->nullable();
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            $table->string('owner')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('address_link')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospitals');
    }
};
