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
        Schema::create('student_task_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_session_id');
            $table->string('student_id');
            $table->decimal('score', 5, 2);
            $table->enum('status', ['in_progress', 'finished']);
            $table->integer('duration');
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_task_sessions');
    }
};
