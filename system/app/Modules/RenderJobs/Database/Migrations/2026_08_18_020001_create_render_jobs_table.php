<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('render_jobs', function (Blueprint $table): void {
            $table->id();
            $table->ulid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('credit_reservation_id')->nullable()->constrained('credit_reservations')->nullOnDelete();
            $table->string('tool');
            $table->string('status')->default('queued')->index();
            $table->unsignedTinyInteger('progress')->default(0);
            $table->string('model')->nullable();
            $table->unsignedSmallInteger('scale')->default(1);
            $table->string('output_format', 16)->default('png');
            $table->json('settings')->nullable();
            $table->unsignedInteger('credits_cost')->default(1);
            $table->string('source_disk')->default('public');
            $table->string('source_path');
            $table->string('source_name');
            $table->string('source_mime')->nullable();
            $table->unsignedBigInteger('source_size')->default(0);
            $table->unsignedInteger('source_width')->nullable();
            $table->unsignedInteger('source_height')->nullable();
            $table->unsignedInteger('target_width')->nullable();
            $table->unsignedInteger('target_height')->nullable();
            $table->string('output_disk')->nullable();
            $table->string('output_path')->nullable();
            $table->string('output_name')->nullable();
            $table->string('output_mime')->nullable();
            $table->unsignedBigInteger('output_size')->nullable();
            $table->unsignedInteger('output_width')->nullable();
            $table->unsignedInteger('output_height')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'tool']);
            $table->index(['tool', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('render_jobs');
    }
};
