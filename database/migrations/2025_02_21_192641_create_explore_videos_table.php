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
        Schema::create('explore_videos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('youtubeVideo_id', 255)->unique();
            $table->json('video_resource')->nullable();
            $table->string('category', 255)->nullable();
            $table->datetimes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('explore_videos');
    }
};
