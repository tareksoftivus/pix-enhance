<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            // Reference number shown to users (e.g. TKT-000123). Human-friendly, unique.
            $table->string('reference', 20)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('subject');
            $table->text('body');
            $table->string('category')->nullable();
            $table->string('priority', 20)->default('medium');   // low|medium|high|urgent
            $table->string('status', 20)->default('open');       // open|pending|resolved|closed
            // Timestamp of the most recent reply, used to sort the admin queue by activity.
            $table->timestamp('last_reply_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('priority');
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
