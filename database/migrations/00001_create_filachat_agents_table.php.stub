<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('filachat_agents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agentable_id');
            $table->string('agentable_type');
            $table->timestamps();

            $table->unique(['agentable_id', 'agentable_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('filachat_agents');
    }
};
