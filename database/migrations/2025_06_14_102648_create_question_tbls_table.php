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
        Schema::create('question_tbls', function (Blueprint $table) {
            $table->id();
            $table->json('question');  // contains text + image paths
            $table->string('type');
            $table->json('options');   // all options with text + images
            $table->string('answer');    // text + images
            $table->json('solution');  // text + images
            $table->float('positive_marks')->default(1);
            $table->float('negative_marks')->default(0);
            $table->boolean('is_public')->default(false);
            $table->foreignId('category_id')->nullable()->constrained('question_categories')->onDelete('cascade');
            $table->enum('category_depth_index',['subject','chapter','topic']);
            $table->timestamps();
        });
    }

    /**'subject','','topic'
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_tbls');
    }
};
