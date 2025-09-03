<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name')->nullable();
            $table->string('host_label')->nullable();
            $table->string('hostname')->nullable();
            $table->string('version')->nullable();
            $table->string('token_hash');
            $table->unsignedInteger('watcher_count')->default(0);
            $table->timestamp('last_seen_at')->nullable();
            $table->json('last_payload')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
