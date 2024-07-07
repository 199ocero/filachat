<?php

namespace JaOcero\FilaChat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FilaChatConversation extends Model
{
    protected $table = 'filachat_conversations';

    protected $fillable = [
        'senderable_id',
        'senderable_type',
        'receiverable_id',
        'receiverable_type',
    ];

    public function senderable(): MorphTo
    {
        return $this->morphTo();
    }

    public function receiverable(): MorphTo
    {
        return $this->morphTo();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(FilaChatMessage::class, 'filachat_conversation_id', 'id');
    }

    public function latestMessage()
    {
        return $this->messages()->latest()->first();
    }

    public function getLastMessageTimeAttribute()
    {
        $latestMessage = $this->latestMessage();

        return $latestMessage ? $latestMessage->created_at->shortAbsoluteDiffForHumans() : null;
    }

    public function getLatestMessageAttribute()
    {
        $latestMessage = $this->latestMessage();

        return $latestMessage ? $latestMessage->message : null;
    }

    public function getUnreadCountAttribute()
    {
        return $this->messages()
            ->whereNull('last_read_at')
            ->where('senderable_type', auth()->user()->getMorphClass())
            ->where('senderable_id', '!=', auth()->id())
            ->count();
    }

    public function getSenderNameAttribute()
    {
        return $this->getUserName($this->senderable, config('chat-support.sender_name_column', 'name'));
    }

    public function getReceiverNameAttribute()
    {
        return $this->getUserName($this->receiverable, config('chat-support.receiver_name_column', 'name'));
    }

    public function getOtherPersonNameAttribute()
    {
        $authUserId = auth()->user()->id;

        if ($this->senderable_id === $authUserId) {
            return $this->getUserName($this->receiverable, config('chat-support.receiver_name_column', 'name'));
        }

        if ($this->receiverable_id === $authUserId) {
            return $this->getUserName($this->senderable, config('chat-support.sender_name_column', 'name'));
        }

        return 'Unknown Name';
    }

    protected function getUserName($user, $column)
    {
        return $user ? $user->{$column} : 'Unknown Name';
    }
}
