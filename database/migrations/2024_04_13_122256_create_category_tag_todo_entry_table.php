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
        Schema::create('category_tag_todo_entry', function (Blueprint $table) {
            $table->foreignId('category_tags_id')->constrained()->onDelete('cascade');
            $table->foreignId('todo_entries_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->primary(['category_tags_id','todo_entries_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_tag_todo_entry');
    }
};
