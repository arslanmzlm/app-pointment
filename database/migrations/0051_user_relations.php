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
        Schema::table('users', function (Blueprint $table) {
            $table->after('type', function (Blueprint $table) {
                $table->foreignId('hospital_id')
                    ->nullable()
                    ->constrained()
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
                $table->foreignId('doctor_id')
                    ->nullable()
                    ->constrained()
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
                $table->foreignId('patient_id')
                    ->nullable()
                    ->constrained()
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('hospital_id');
            $table->dropConstrainedForeignId('doctor_id');
            $table->dropConstrainedForeignId('patient_id');
        });
    }
};
