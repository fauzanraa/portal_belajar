<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_material_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('material_session_id');
            $table->string('student_id');
            $table->enum('status', ['visible', 'hidden']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_material_sessions');
    }
};
