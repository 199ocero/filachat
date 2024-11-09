<?php

namespace JaOcero\FilaChat\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
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

    public function createConversationAction(string $name, bool $isLabelHidden = false): Action
    {
        $isRoleEnabled = config('filachat.enable_roles');

        $isAgent = auth()->user()->isAgent();

        return Action::make($name)
            ->label(__('Create Conversation'))
            ->hiddenLabel($isLabelHidden)
            ->icon('heroicon-o-chat-bubble-left-ellipsis')
            ->extraAttributes([
                'class' => 'w-full',
            ])
            ->form([
                Forms\Components\Radio::make('type')
                    ->options([
                        'personal' => __('One-on-one conversation'),
                        'group' => __('Group chat'),
                    ])
                    ->reactive()
                    ->label(__('Conversion type')),
                Forms\Components\TextInput::make('group_name')
                    ->required()
                    ->visible(function (Forms\Get $get) {
                        return $get('type') === 'group';
                    })
                    ->label(__('Group name')),
                Forms\Components\Select::make('receiverable_id')
                    ->visible(function (Forms\Get $get) {
                        return $get('type') != null;
                    })
                    ->multiple(fn(Forms\Get $get) => $get('type') == 'group')
                    ->label(function (Forms\Get $get) use ($isRoleEnabled, $isAgent) {

                        if ($get('type') === 'group') {
                            return __('Group members');
                        }

                        if ($isRoleEnabled) {
                            if ($isAgent) {
                                return __('To User');
                            }

                            return __('To Agent');
                        }

                        return __('To');
                    })
                    ->placeholder(function () use ($isRoleEnabled, $isAgent) {
                        if ($isRoleEnabled && !$isAgent) {
                            return __('Select Agent by Name or Email');
                        }

                        return __('Select User by Name or Email');
                    })
                    ->searchPrompt(function () use ($isRoleEnabled, $isAgent) {
                        if ($isRoleEnabled && !$isAgent) {
                            return __('Search Agent by Name or Email');
                        }

                        return __('Search User by Name or Email');
                    })
                    ->loadingMessage(function () use ($isRoleEnabled, $isAgent) {
                        if ($isRoleEnabled && !$isAgent) {
                            return __('Loading Agents...');
                        }

                        return __('Loading Users...');
                    })
                    ->noSearchResultsMessage(function () use ($isRoleEnabled, $isAgent) {
                        if ($isRoleEnabled && !$isAgent) {
                            return __('No Agents Found.');
                        }

                        return __('No Users Found.');
                    })
                    ->getSearchResultsUsing(fn(string $search): array => ChatListService::make()->getSearchResults($search)->toArray())
                    ->getOptionLabelUsing(fn($value): ?string => ChatListService::make()->getOptionLabel($value))
                    ->searchable()
                    ->required(),
                Forms\Components\Textarea::make('message')
                    ->label(__('Your Message'))
                    ->placeholder(__('Write a message...'))
                    ->required()
                    ->autosize(),
            ])->modalSubmitActionLabel(__('Add'))
            ->modalWidth(MaxWidth::Large)
            ->action(fn(array $data) => ChatListService::make()->createConversation($data));
    }

    public function createConversationSmallSizeAction(): Action
    {
        return $this->createConversationAction(name: 'createConversationSmallSizeAction', isLabelHidden: true);
    }

    public function render(): Application|Factory|View|\Illuminate\View\View
    {
        return view('filachat::filachat.components.chat-list');
    }
}
