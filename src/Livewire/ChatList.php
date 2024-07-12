<?php

namespace JaOcero\FilaChat\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Support\Enums\MaxWidth;
use JaOcero\FilaChat\Events\FilaChatMessageEvent;
use JaOcero\FilaChat\Models\FilaChatConversation;
use JaOcero\FilaChat\Models\FilaChatMessage;
use JaOcero\FilaChat\Pages\FilaChat;
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
                        } else {
                            return 'To';
                        }
                    })
                    ->getSearchResultsUsing(function (string $search) use ($isRoleEnabled, $isAgent) {
                        $authId = auth()->id();
                        $searchTerm = '%' . trim($search) . '%';

                        $senderNameColumn = config('filachat.sender_name_column');
                        $receiverNameColumn = config('filachat.receiver_name_column');

                        $name = $senderNameColumn ?? $receiverNameColumn;

                        $userModelClass = config('filachat.user_model');
                        $agentModelClass = config('filachat.agent_model');

                        if ($isRoleEnabled) {

                            $agentIds = config('filachat.agent_model')::getAllAgentIds();

                            if ($isAgent) {
                                return $userModelClass::query()
                                    ->whereNotIn('id', $agentIds)
                                    ->where(function ($query) use ($searchTerm, $senderNameColumn, $receiverNameColumn) {
                                        $query->where($senderNameColumn, 'like', '%' . $searchTerm . '%')
                                            ->orWhere($receiverNameColumn, 'like', '%' . $searchTerm . '%');
                                    })
                                    ->get()
                                    ->mapWithKeys(function ($item) use ($name) {
                                        return ['user_' . $item->id => $item->{$name}];
                                    });
                            }

                            return $agentModelClass::query()
                                ->whereIn('id', $agentIds)
                                ->where(function ($query) use ($searchTerm, $senderNameColumn, $receiverNameColumn) {
                                    $query->where($senderNameColumn, 'like', '%' . $searchTerm . '%')
                                        ->orWhere($receiverNameColumn, 'like', '%' . $searchTerm . '%');
                                })
                                ->get()
                                ->mapWithKeys(function ($item) use ($name) {
                                    return ['agent_' . $item->id => $item->{$name}];
                                });
                        } else {
                            if ($userModelClass === $agentModelClass) {
                                return $userModelClass::query()
                                    ->whereNot('id', $authId)
                                    ->where(function ($query) use ($searchTerm, $senderNameColumn, $receiverNameColumn) {
                                        $query->where($senderNameColumn, 'like', '%' . $searchTerm . '%')
                                            ->orWhere($receiverNameColumn, 'like', '%' . $searchTerm . '%');
                                    })
                                    ->get()
                                    ->mapWithKeys(function ($item) use ($name) {
                                        return ['user_' . $item->id => $item->{$name}];
                                    });
                            }

                            $userModel = $userModelClass::query()
                                ->whereNot('id', $authId)
                                ->where(function ($query) use ($searchTerm, $senderNameColumn, $receiverNameColumn) {
                                    $query->where($senderNameColumn, 'like', '%' . $searchTerm . '%')
                                        ->orWhere($receiverNameColumn, 'like', '%' . $searchTerm . '%');
                                })
                                ->get()
                                ->mapWithKeys(function ($item) use ($name) {
                                    return ['user_' . $item->id => $item->{$name}];
                                });

                            $agentModel = $agentModelClass::query()
                                ->whereNot('id', $authId)
                                ->where(function ($query) use ($searchTerm, $senderNameColumn, $receiverNameColumn) {
                                    $query->where($senderNameColumn, 'like', '%' . $searchTerm . '%')
                                        ->orWhere($receiverNameColumn, 'like', '%' . $searchTerm . '%');
                                })
                                ->get()
                                ->mapWithKeys(function ($item) use ($name) {
                                    return ['agent_' . $item->id => $item->{$name}];
                                });

                            return $userModel->merge($agentModel);
                        }
                    })
                    ->searchable()
                    ->required(),
                Forms\Components\Textarea::make('message')
                    ->label('Your Message')
                    ->placeholder('Write a message...')
                    ->required()
                    ->autosize(),
            ])
            ->modalWidth(MaxWidth::Large)
            ->action(function (array $data) {

                try {
                    $receiverableId = $data['receiverable_id'];

                    if (preg_match('/^user_(\d+)$/', $receiverableId, $matches)) {
                        $receiverableType = config('filachat.user_model');
                        $receiverableId = (int) $matches[1];
                    }

                    if (preg_match('/^agent_(\d+)$/', $receiverableId, $matches)) {
                        $receiverableType = config('filachat.agent_model');
                        $receiverableId = (int) $matches[1];
                    }

                    $foundConversation = FilaChatConversation::query()
                        ->where(function ($query) use ($receiverableId, $receiverableType) {
                            $query->where(function ($query) {
                                $query->where('senderable_id', auth()->id())
                                    ->where('senderable_type', auth()->user()::class);
                            })
                                ->orWhere(function ($query) use ($receiverableId, $receiverableType) {
                                    $query->where('senderable_id', $receiverableId)
                                        ->where('senderable_type', $receiverableType);
                                });
                        })
                        ->where(function ($query) use ($receiverableId, $receiverableType) {
                            $query->where(function ($query) use ($receiverableId, $receiverableType) {
                                $query->where('receiverable_id', $receiverableId)
                                    ->where('receiverable_type', $receiverableType);
                            })
                                ->orWhere(function ($query) {
                                    $query->where('receiverable_id', auth()->id())
                                        ->where('receiverable_type', auth()->user()::class);
                                });
                        })
                        ->first();

                    if (! $foundConversation) {
                        $conversation = FilaChatConversation::query()->create([
                            'senderable_id' => auth()->id(),
                            'senderable_type' => auth()->user()::class,
                            'receiverable_id' => $receiverableId,
                            'receiverable_type' => $receiverableType,
                        ]);
                    } else {
                        $conversation = $foundConversation;
                    }

                    $message = FilaChatMessage::query()->create([
                        'filachat_conversation_id' => $conversation->id,
                        'senderable_id' => auth()->id(),
                        'senderable_type' => auth()->user()::class,
                        'receiverable_id' => $receiverableId,
                        'receiverable_type' => $receiverableType,
                        'message' => $data['message'],
                    ]);

                    $conversation->updated_at = now();

                    $conversation->save();

                    broadcast(new FilaChatMessageEvent(
                        $conversation->id,
                        $message->id,
                        $receiverableId,
                        auth()->id(),
                    ));

                    return $this->redirect(FilaChat::getUrl(tenant: filament()->getTenant()) . '/' . $conversation->id);
                } catch (\Exception $exception) {
                    Notification::make()
                        ->title('Something went wrong')
                        ->body($exception->getMessage())
                        ->danger()
                        ->persistent()
                        ->send();
                }
            });
    }

    public function render()
    {
        return view('filachat::filachat.components.chat-list');
    }
}
