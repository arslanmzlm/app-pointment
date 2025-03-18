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
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->string('section')->index();
            $table->boolean('active')->default(false);
            $table->string('title');
            $table->string('slug')->nullable()->unique();
            $table->string('subtitle')->nullable();
            $table->string('top_title')->nullable();
            $table->string('alt_title')->nullable();
            $table->string('link')->nullable();
            $table->string('link_label')->nullable();
            $table->string('image')->nullable();
            $table->string('mobile_image')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedTinyInteger('order')->nullable();
            $table->text('description')->nullable();
            $table->text('body')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
