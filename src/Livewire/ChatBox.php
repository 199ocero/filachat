<?php

namespace JaOcero\FilaChat\Livewire;

use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use JaOcero\FilaChat\Events\FilaChatMessageEvent;
use JaOcero\FilaChat\Events\FilaChatMessageReadEvent;
use JaOcero\FilaChat\Models\FilaChatMessage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ChatBox extends Component implements HasForms
{
    use InteractsWithForms;
    use WithPagination;

    public $selectedConversation;

    public $currentPage = 1;

    public Collection $messages;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();

        if ($this->selectedConversation) {
            $this->messages = collect();
            $this->loadMoreMessages();
        }
    }

    public function form(Form $form): Form
    {
        $isRoleEnabled = config('filachat.enable_roles');
        $isAgent = auth()->user()->isAgent();

        if ($this->selectedConversation) {
            if (auth()->id() === $this->selectedConversation->receiverable_id) {
                $isOtherPersonAgent = $this->selectedConversation->senderable->isAgent();
            } else {
                $isOtherPersonAgent = $this->selectedConversation->receiverable->isAgent();
            }
        } else {
            $isOtherPersonAgent = false;
        }

        return $form
            ->schema([
                Forms\Components\Textarea::make('message')
                    ->hiddenLabel()
                    ->placeholder(function () use ($isRoleEnabled, $isAgent, $isOtherPersonAgent) {
                        if ($isRoleEnabled) {

                            // if both in the conversation are normal users
                            if (! $isAgent && ! $isOtherPersonAgent) {
                                return 'You cannot write a message for other user...';
                            }

                            // if both in the conversation are agents
                            if ($isAgent && $isOtherPersonAgent) {
                                return 'You cannot write a message for other agent...';
                            }
                        }

                        return 'Write a message...';
                    })
                    ->required()
                    ->rows(1)
                    ->autosize()
                    ->disabled(function () use ($isRoleEnabled, $isAgent, $isOtherPersonAgent) {

                        if ($isRoleEnabled) {

                            // if both in the conversation are normal users
                            if (! $isAgent && ! $isOtherPersonAgent) {
                                return true;
                            }

                            // if both in the conversation are agents
                            if ($isAgent && $isOtherPersonAgent) {
                                return true;
                            }

                            // if one in the conversation is an agent
                            if ($isAgent && ! $isOtherPersonAgent) {
                                return false;
                            }

                            // if one in the conversation is a normal user
                            if (! $isAgent && $isOtherPersonAgent) {
                                return false;
                            }
                        }

                        // if roles are not enabled
                        return false;
                    }),
            ])
            ->columns('full')
            ->statePath('data');
    }

    public function sendMessage(): void
    {
        try {
            DB::transaction(function () {
                if (auth()->id() === $this->selectedConversation->receiverable_id) {
                    $receiverableId = $this->selectedConversation->senderable_id;
                    $receiverableType = $this->selectedConversation->senderable_type;
                } else {
                    $receiverableId = $this->selectedConversation->receiverable_id;
                    $receiverableType = $this->selectedConversation->receiverable_type;
                }

                $newMessage = FilaChatMessage::query()->create([
                    'filachat_conversation_id' => $this->selectedConversation->id,
                    'senderable_id' => auth()->id(),
                    'senderable_type' => auth()->user()::class,
                    'receiverable_id' => $receiverableId,
                    'receiverable_type' => $receiverableType,
                    'message' => $this->data['message'],
                ]);

                $this->form->fill();

                $this->selectedConversation->updated_at = now();

                $this->selectedConversation->save();

                broadcast(new FilaChatMessageEvent(
                    $this->selectedConversation->id,
                    $newMessage->id,
                    $receiverableId,
                    auth()->id(),
                ));
            });
        } catch (\Exception $exception) {
            Notification::make()
                ->title('Something went wrong')
                ->body($exception->getMessage())
                ->danger()
                ->persistent()
                ->send();
        }
    }

    #[On('echo:filachat,.JaOcero\\FilaChat\\Events\\FilaChatMessageEvent')]
    public function broadcastNewMessage($data)
    {
        if ($data['type'] === FilaChatMessageEvent::class) {

            /**
             * This will only be executed if the conversation
             * is the selected conversation
             */
            if ($data['conversationId'] === $this->selectedConversation->id) {

                $this->dispatch('chat-box-scroll-to-bottom');

                $message = FilaChatMessage::find($data['messageId']);

                $this->messages->prepend($message);

                if ($message->receiverable_id === auth()->id() && $message->receiverable_type === auth()->user()::class) {
                    $message->last_read_at = now();
                    $message->save();

                    broadcast(new FilaChatMessageReadEvent($this->selectedConversation->id));
                }
            }

            /**
             * Refresh the conversation list if the sender or receiver
             * is the current authenticated user
             */
            if ($data['receiverId'] === auth()->id() || $data['senderId'] === auth()->id()) {
                $this->dispatch('load-conversations');
            }
        }
    }

    public function loadMoreMessages()
    {
        $this->messages->push(...$this->paginator->getCollection());

        $this->currentPage = $this->currentPage + 1;

        $this->dispatch('chat-box-preserve-scroll-position');
    }

    #[Computed()]
    public function paginator()
    {
        return $this->selectedConversation->messages()->latest()->paginate(10, ['*'], 'page', $this->currentPage);
    }

    public function render()
    {
        return view('filachat::filachat.components.chat-box');
    }
}
