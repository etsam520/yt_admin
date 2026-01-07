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
        Schema::create('ppt_tbls', function (Blueprint $table) {
            $table->id();
            $table->string('en_path')->nullable();
            $table->string('hi_path')->nullable();
            $table->string('u_path')->nullable();
            $table->boolean('is_public')->default(false);
            $table->boolean('is_converted')->default(false);
            $table->foreignId('stream_content_id')
                ->constrained('stream_contents')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppt_tbls');
    }
};
