<?php

namespace JaOcero\FilaChat\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use JaOcero\FilaChat\Models\FilaChatConversation;
use JaOcero\FilaChat\Models\FilaChatRole;

trait HasFilaChat
{
    public function sentConversations(): MorphMany
    {
        return $this->morphMany(FilaChatConversation::class, 'senderable');
    }

    public function receivedConversations(): MorphMany
    {
        return $this->morphMany(FilaChatConversation::class, 'receiverable');
    }

    public function allConversations()
    {
        return FilaChatConversation::where(function ($query) {
            $query->where('senderable_type', self::class)
                ->where('senderable_id', $this->id);
        })->orWhere(function ($query) {
            $query->where('receiverable_type', self::class)
                ->where('receiverable_id', $this->id);
        });
    }

    public function role(): MorphOne
    {
        return $this->morphOne(FilaChatRole::class, 'userable');
    }
}
