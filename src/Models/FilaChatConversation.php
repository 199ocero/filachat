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

        return $latestMessage ? $latestMessage->created_at : null;
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
        return $this->getName($this->senderable, config('filachat.sender_name_column'));
    }

    public function getReceiverNameAttribute()
    {
        return $this->getName($this->receiverable, config('filachat.receiver_name_column'));
    }

    public function getOtherPersonNameAttribute()
    {
        $authUserId = auth()->user()->id;

        if ($this->senderable_id === $authUserId) {
            return $this->getName($this->receiverable, config('filachat.receiver_name_column'));
        }

        if ($this->receiverable_id === $authUserId) {
            return $this->getName($this->senderable, config('filachat.sender_name_column'));
        }

        return 'Unknown Name';
    }

    protected function getName($user, $column)
    {
        return $user ? $user->{$column} : 'Unknown Name';
    }
}
