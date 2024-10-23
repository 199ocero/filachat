<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('filachat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('filachat_conversation_id')->constrained('filachat_conversations')->onDelete('cascade');
            $table->text('message')->nullable();
            $table->json('attachments')->nullable();
            $table->json('original_attachment_file_names')->nullable();
            $table->json('reactions')->nullable();
            $table->boolean('is_starred')->default(false);
            $table->json('metadata')->nullable();
            $table->foreignId('reply_to_message_id')->nullable()->constrained('filachat_messages')->onDelete('cascade');
            $table->unsignedBigInteger('senderable_id');
            $table->string('senderable_type');
            $table->unsignedBigInteger('receiverable_id');
            $table->string('receiverable_type');
            $table->timestamp('last_read_at')->nullable();
            $table->timestamp('edited_at')->nullable();
            $table->timestamp('sender_deleted_at')->nullable();
            $table->timestamp('receiver_deleted_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Adding indexes to polymorphic relationship columns
            $table->index(['senderable_id', 'senderable_type']);
            $table->index(['receiverable_id', 'receiverable_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('filachat_messages');
    }
};
