<?php

namespace JaOcero\FilaChat\Pages;

use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;
use JaOcero\FilaChat\Events\FilaChatMessageReadEvent;
use JaOcero\FilaChat\Models\FilaChatConversation;
use JaOcero\FilaChat\Models\FilaChatMessage;

class FilaChat extends Page
{
    protected static string $view = 'filachat::filachat.index';

    public $selectedConversation;

    public function mount(?int $id = null): void
    {
        if ($id) {
            $this->selectedConversation = FilaChatConversation::findOrFail($id);

            $message = FilaChatMessage::query()
                ->where('filachat_conversation_id', $this->selectedConversation->id)
                ->where('last_read_at', null)
                ->where('receiverable_id', auth()->id())
                ->where('receiverable_type', auth()->user()::class);

            if ($message->exists()) {
                $message->update(['last_read_at' => now()]);

                broadcast(new FilaChatMessageReadEvent($this->selectedConversation->id));
            }
        }
    }

    public static function getSlug(): string
    {
        return config('filachat.slug') . '/{id?}';
    }

    public function getTitle(): string
    {
        return __(config('filachat.navigation_label'));
    }

    public static function getNavigationLabel(): string
    {
        return __(config('filachat.navigation_label'));
    }

    public static function getNavigationIcon(): string | Htmlable | null
    {
        return config('filachat.navigation_icon');
    }

    public function getMaxContentWidth(): MaxWidth | string | null
    {
        return config('filachat.max_content_width');
    }

    public function getHeading(): string | Htmlable
    {
        return ''; // should be empty by default
    }
}
