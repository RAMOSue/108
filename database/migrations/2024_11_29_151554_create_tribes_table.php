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
        Schema::create('tribes', function (Blueprint $table) { //recipes
            $table->id();  // Auto-incrementing ID column
            $table->string('tribe_name');  // Title of the recipe
            $table->text('description'); //description
            $table->text('language'); // ingredients
            $table->text('cultural_practices'); //procedure
            $table->text('population'); // prep_time
            $table->text('region'); // servings
            $table->text('url_image')->nullable();  // Make 'url_image' nullable
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');  // Foreign key for user (creator)
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');  // Foreign key for category
            $table->timestamps();  // 'created_at' and 'updated_at' columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tribes');
    }
};