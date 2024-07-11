<?php

namespace JaOcero\FilaChat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FilaChatMessage extends Model
{
    protected $table = 'filachat_messages';

    protected $fillable = [
        'filachat_conversation_id',
        'senderable_id',
        'senderable_type',
        'receiverable_id',
        'receiverable_type',
        'last_read_at',
        'sender_deleted_at',
        'receiver_deleted_at',
        'message',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(FilaChatConversation::class, 'filachat_conversation_id', 'id');
    }

    public function senderable(): MorphTo
    {
        return $this->morphTo();
    }

    public function receiverable(): MorphTo
    {
        return $this->morphTo();
    }

    public function isRead(): bool
    {
        return $this->last_read_at !== null;
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
