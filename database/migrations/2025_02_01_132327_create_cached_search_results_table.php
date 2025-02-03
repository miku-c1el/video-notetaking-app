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
        Schema::create('cached_search_results', function (Blueprint $table) {
            $table->increments('id');
            $table->string('query', 255)->unique();
            $table->json('result'); 
            $table->dateTime('cached_at')->useCurrent();
            $table->dateTime('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cached_search_results');
    }
};
