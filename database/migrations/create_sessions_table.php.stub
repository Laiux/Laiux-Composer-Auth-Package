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
            $table->integer("expire_time");
            $table->string("device");
            $table->string("platform");
            $table->string("browser");
            $table->boolean("is_desktop");
            $table->boolean("is_phone");
            $table->json("ip_info");
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
