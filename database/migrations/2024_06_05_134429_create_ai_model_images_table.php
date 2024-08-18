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
        Schema::create('ai_model_images', function (Blueprint $table) {
            $table->id();
            //[ id url nsfwLevel width height ]
            $table->string('model_id',32)->index();
            $table->string('thumb',1024)->nullable();
            $table->integer('width');
            $table->integer('height');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_model_images');
    }
};
