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
        Schema::create('task_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_task_session_id');
            $table->unsignedBigInteger('task_question_id');
            $table->string('student_id');
            $table->json('answer');
            $table->text('flowchart_img');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_answers');
    }
};
