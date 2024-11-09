<?php

namespace JaOcero\FilaChat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public function group(): BelongsTo
    {
        return $this->belongsTo(FilaChatGroup::class, 'receiverable_id');
    }

    public function getLastMessageTimeAttribute()
    {
        $latestMessage = $this->latestMessage();

        return $latestMessage ? $latestMessage->created_at : null;
    }

    public function latestMessage()
    {
        return $this->messages()->latest()->first();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(FilaChatMessage::class, 'filachat_conversation_id', 'id');
    }

    public function getLatestMessageAttribute()
    {
        $latestMessage = $this->latestMessage();

        if ($latestMessage->message) {
            return $latestMessage->message;
        }

        $attachmentCount = count($latestMessage->attachments);
        $fileWord = $attachmentCount > 1 ? 'files' : 'file';

        return 'Sent ' . $attachmentCount . ' ' . $fileWord . '.';
    }

    public function getUnreadCountAttribute(): int
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

    protected function getName($user, $column)
    {
        return $user ? $user->{$column} : 'Unknown Name';
    }

    public function getReceiverNameAttribute()
    {
        return $this->getName($this->receiverable, config('filachat.receiver_name_column'));
    }

    public function getOtherPersonNameAttribute()
    {
        if ($this->isGroup()) {
            return $this->getName($this->group, config('filachat.group_name_column'));
        } else {
            $authUserId = auth()->user()->id;

            if ($this->senderable_id === $authUserId) {
                return $this->getName($this->receiverable, config('filachat.receiver_name_column'));
            }

            if ($this->receiverable_id === $authUserId) {
                return $this->getName($this->senderable, config('filachat.sender_name_column'));
            }
        }

        return 'Unknown Name';
    }

    public function isGroup(): bool
    {
        return $this->receiverable_type === FilaChatGroup::class;
    }

    public function getIsSenderAttribute(): bool
    {
        $latestMessage = $this->latestMessage();

        if ($latestMessage->senderable_id === auth()->user()->id) {
            return true;
        }

        return false;
    }
}
