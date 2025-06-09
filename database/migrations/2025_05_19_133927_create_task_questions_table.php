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
        Schema::create('task_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_session_id');
            $table->text('question');
            $table->enum('type', ['main', 'bonus']);
            $table->json('correct_answer');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_questions');
    }
};
