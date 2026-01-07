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
        Schema::create('stream_contents', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['video', 'audio', 'question', 'pdf', 'ppt']);
            $table->string('target_table');
            $table->unsignedBigInteger('target_id');
            $table->foreignId('course_directory_id')->nullable()
                ->constrained('course_directories')
                ->onDelete('cascade');
            $table->foreignId('trade_node_id')->nullable()
                    ->constrained('trade_nodes')
                    ->onDelete('cascade');
            $table->foreignId('question_sets_id')->nullable();
            // ->constrained('question_sets')
            // ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stream_contents');
    }
};
