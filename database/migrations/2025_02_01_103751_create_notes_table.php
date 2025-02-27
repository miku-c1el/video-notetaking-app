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
        Schema::create('notes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 255)->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('youtubeVideo_id', 255);
            // $table->string('thumbnail', 255);
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('youtubeVideo_id')->references('youtubeVideo_id')->on('videos')->onUpdate('cascade');
            $table->datetimes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
};
