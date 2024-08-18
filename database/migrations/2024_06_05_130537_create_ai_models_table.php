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
        Schema::create('ai_models', function (Blueprint $table) {
            $table->id();
            $table->string('model_id',32)->index();  
            $table->string('hash',32)->index();  
            $table->string('name',256);   
            $table->string('type',32);      //checkpoint lora  
            $table->boolean('nsfw')->default(false);
            $table->text('tags')->nullable();
            $table->string('base_model',32);   
            $table->integer('base_size')->default(0);
            $table->string('sd_name',256);      
            $table->string('download_url',1024)->nullable(); 
            $table->string('thumb',1024)->nullable();
            $table->boolean('is_service')->default(true);
            $table->string('engine',10);
            $table->boolean('favored')->default(false);
            $table->boolean('is_download')->default(false);
            $table->string('prompt',1024);     
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_models');
    }
};
