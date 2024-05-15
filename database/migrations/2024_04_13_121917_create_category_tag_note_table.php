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
        Schema::create('category_tag_note', function (Blueprint $table) {
            $table->foreignId('category_tag_id')->constrained('category_tags')->onDelete('cascade');
            $table->foreignId('note_id')->constrained('notes')->onDelete('cascade');
            $table->timestamps();
            $table->primary(['category_tag_id', 'note_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_tag_note');
    }
};
