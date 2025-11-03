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
        Schema::create('saloon_loggers', function (Blueprint $table) {
            $table->id();
            $table->ulid('trace_id')->index();
            $table->string('phase', 20);
            $table->string('method', 10);
            $table->text('endpoint');
            $table->json('headers')->nullable();
            $table->json('query')->nullable();
            $table->json('payload')->nullable();
            $table->unsignedSmallInteger('status')->nullable();
            $table->longText('response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saloon_loggers');
    }
};
