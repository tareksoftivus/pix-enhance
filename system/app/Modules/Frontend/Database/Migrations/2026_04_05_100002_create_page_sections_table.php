<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('pages')->cascadeOnDelete();
            $table->foreignId('frontend_section_id')->constrained('frontend_sections')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('visibility_rules')->nullable();
            $table->timestamps();

            $table->unique(['page_id', 'frontend_section_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_sections');
    }
};
