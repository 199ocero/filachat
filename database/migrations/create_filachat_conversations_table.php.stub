<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('filachat_conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('senderable_id');
            $table->string('senderable_type');
            $table->unsignedBigInteger('receiverable_id');
            $table->string('receiverable_type');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('filachat_conversations');
    }
};
