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
        Schema::create('moments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('note_id');
            $table->string('title', 255)->nullable();
            $table->text('content')->nullable();
            $table->unsignedInteger('timestamp');
            $table->datetimes();
            $table->foreign('note_id')->references('id')->on('notes')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moments');
    }
};
