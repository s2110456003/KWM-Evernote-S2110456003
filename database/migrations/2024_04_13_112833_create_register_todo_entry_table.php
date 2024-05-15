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
        Schema::create('register_todo_entry', function (Blueprint $table) {
            $table->foreignId('todo_entry_id')->constrained('todo_entries')->onDelete('cascade');
            $table->foreignId('register_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->primary(['todo_entry_id','register_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('register_todo_entry');
    }
};
