<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Veza s nastavnikom
            $table->string('title'); // Naslov rada na hrvatskom
            $table->string('title_en'); // Naslov rada na engleskom
            $table->text('description'); // Opis zadatka
            $table->enum('study_type', ['professional', 'undergraduate', 'graduate']); // Tip studija: stručni, preddiplomski, diplomski
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};
