<?php

namespace JaOcero\FilaChat\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\MaxWidth;
use JaOcero\FilaChat\Services\ChatListService;
use Livewire\Attributes\On;
use Livewire\Component;

class ChatList extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public $conversations;

    public $selectedConversation;

    public function mount(): void
    {
        $this->loadConversations();
    }

    #[On('load-conversations')]
    public function loadConversations(): void
    {
        $this->conversations = auth()->user()->allConversations()
            ->with(['senderable', 'receiverable', 'messages' => function ($query) {
                $query->latest();
            }])
            ->latest('updated_at')
            ->get();
    }

    public function createConversationMediumSizeAction(): Action
    {
        return $this->createConversationAction(name: 'createConversationMediumSize', isLabelHidden: false);
    }

    public function createConversationSmallSizeAction(): Action
    {
        return $this->createConversationAction(name: 'createConversationSmallSizeAction', isLabelHidden: true);
    }

    public function createConversationAction(string $name, bool $isLabelHidden = false): Action
    {
        $isRoleEnabled = config('filachat.enable_roles');

        $isAgent = auth()->user()->isAgent();

        return Action::make($name)
            ->label('Create Conversation')
            ->hiddenLabel($isLabelHidden)
            ->icon('heroicon-o-chat-bubble-left-ellipsis')
            ->extraAttributes([
                'class' => 'w-full',
            ])
            ->form([
                Forms\Components\Select::make('receiverable_id')
                    ->label(function () use ($isRoleEnabled, $isAgent) {
                        if ($isRoleEnabled) {
                            if ($isAgent) {
                                return 'To User';
                            }

                            return 'To Agent';
                        }

                        return 'To';
                    })
                    ->placeholder(function () use ($isRoleEnabled, $isAgent) {
                        if ($isRoleEnabled && ! $isAgent) {
                            return 'Select Agent by Name or Email';
                        }

                        return 'Select User by Name or Email';
                    })
                    ->searchPrompt(function () use ($isRoleEnabled, $isAgent) {
                        if ($isRoleEnabled && ! $isAgent) {
                            return 'Search Agent by Name or Email';
                        }

                        return 'Search User by Name or Email';
                    })
                    ->loadingMessage(function () use ($isRoleEnabled, $isAgent) {
                        if ($isRoleEnabled && ! $isAgent) {
                            return 'Loading Agents...';
                        }

                        return 'Loading Users...';
                    })
                    ->noSearchResultsMessage(function () use ($isRoleEnabled, $isAgent) {
                        if ($isRoleEnabled && ! $isAgent) {
                            return 'No Agents Found.';
                        }

                        return 'No Users Found.';
                    })
                    ->getSearchResultsUsing(fn (string $search): array => ChatListService::make()->getSearchResults($search)->toArray())
                    ->getOptionLabelUsing(fn ($value): ?string => ChatListService::make()->getOptionLabel($value))
                    ->searchable()
                    ->required(),
                Forms\Components\Textarea::make('message')
                    ->label('Your Message')
                    ->placeholder('Write a message...')
                    ->required()
                    ->autosize(),
            ])
            ->modalWidth(MaxWidth::Large)
            ->action(fn (array $data) => ChatListService::make()->createConversation($data));
    }

    public function render()
    {
        return view('filachat::filachat.components.chat-list');
    }
}
