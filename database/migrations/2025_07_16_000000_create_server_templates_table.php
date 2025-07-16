<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('server_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('egg_id');
            $table->unsignedBigInteger('nest_id');
            $table->json('config')->nullable(); // メモリ・ディスク等の設定
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('server_templates');
    }
};
