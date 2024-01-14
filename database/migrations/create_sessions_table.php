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
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->text("token")->unique();
            $table->integer("issued_date");
            $table->integer("expire_time");
            $table->string("device")->nullable();
            $table->string("platform")->nullable();
            $table->string("browser")->nullable();
            $table->boolean("is_desktop")->nullable();
            $table->boolean("is_phone")->nullable();
            $table->boolean("is_robot")->nullable();
            $table->json("ip_info")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
