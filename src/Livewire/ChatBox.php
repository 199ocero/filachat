<?php

namespace JaOcero\FilaChat\Livewire;

use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use JaOcero\FilaChat\Events\FilaChatMessageEvent;
use JaOcero\FilaChat\Events\FilaChatMessageReadEvent;
use JaOcero\FilaChat\Events\FilaChatMessageReceiverIsAwayEvent;
use JaOcero\FilaChat\Models\FilaChatMessage;
use JaOcero\FilaChat\Traits\CanGetOriginalFileName;
use JaOcero\FilaChat\Traits\CanValidateAudio;
use JaOcero\FilaChat\Traits\CanValidateDocument;
use JaOcero\FilaChat\Traits\CanValidateImage;
use JaOcero\FilaChat\Traits\CanValidateVideo;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ChatBox extends Component implements HasForms
{
    use CanGetOriginalFileName;
    use CanValidateAudio;
    use CanValidateDocument;
    use CanValidateImage;
    use CanValidateVideo;
    use InteractsWithForms;
    use WithPagination;

    public $selectedConversation;

    public $currentPage = 1;

    public Collection $conversationMessages;

    public ?array $data = [];

    public bool $showUpload = false;

    public function mount(): void
    {
        $this->form->fill();

        if ($this->selectedConversation) {
            $this->conversationMessages = collect();
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
                Forms\Components\FileUpload::make('attachments')
                    ->hiddenLabel()
                    ->multiple()
                    ->storeFileNamesIn('original_attachment_file_names')
                    ->fetchFileInformation()
                    ->disk(config('filachat.disk'))
                    ->directory(fn () => config('filachat.disk') == 's3' ? config('filachat.s3.directory') : 'attachments')
                    ->visibility(fn () => config('filachat.disk') == 's3' ? config('filachat.s3.visibility') : 'public')
                    ->acceptedFileTypes(config('filachat.mime_types'))
                    ->maxSize(config('filachat.max_file_size'))
                    ->minSize(config('filachat.min_file_size'))
                    ->maxFiles(config('filachat.max_files'))
                    ->minFiles(config('filachat.min_files'))
                    ->panelLayout('grid')
                    ->extraAttributes([
                        'class' => 'filachat-filepond',
                    ])
                    ->visible(fn () => $this->showUpload),
                Forms\Components\Split::make([
                    Forms\Components\Actions::make([
                        Forms\Components\Actions\Action::make('show_hide_upload')
                            ->hiddenLabel()
                            ->icon('heroicon-m-plus')
                            ->color('gray')
                            ->tooltip('Upload Files')
                            ->action(fn () => $this->showUpload = ! $this->showUpload),
                    ])
                        ->grow(false),
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
                        })
                        ->required(function (Get $get) {
                            if (count($get('attachments')) > 0) {
                                return false;
                            }

                            return true;
                        })
                        ->rows(1)
                        ->autosize()
                        ->grow(true),
                ])
                    ->verticallyAlignEnd(),
            ])
            ->columns('full')
            ->extraAttributes([
                'class' => 'p-1',
            ])
            ->statePath('data');
    }

    public function sendMessage(): void
    {
        $data = $this->form->getState();
        $rawData = $this->form->getRawState();

        try {
            DB::transaction(function () use ($data) {
                if (auth()->id() === $this->selectedConversation->receiverable_id) {
                    $receiverableId = $this->selectedConversation->senderable_id;
                    $receiverableType = $this->selectedConversation->senderable_type;
                } else {
                    $receiverableId = $this->selectedConversation->receiverable_id;
                    $receiverableType = $this->selectedConversation->receiverable_type;
                }

                $newMessage = FilaChatMessage::query()->create([
                    'filachat_conversation_id' => $this->selectedConversation->id,
                    'message' => $data['message'] ?? null,
                    'attachments' => isset($data['attachments']) && count($data['attachments']) > 0 ? $data['attachments'] : null,
                    'original_attachment_file_names' => isset($data['original_attachment_file_names']) && count($data['original_attachment_file_names']) > 0 ? $data['original_attachment_file_names'] : null,
                    'senderable_id' => auth()->id(),
                    'senderable_type' => auth()->user()::class,
                    'receiverable_id' => $receiverableId,
                    'receiverable_type' => $receiverableType,
                ]);

                $this->conversationMessages->prepend($newMessage);

                $this->showUpload = false;

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
            if ($data['conversationId'] && $data['conversationId'] === $this->selectedConversation?->id) {

                $message = FilaChatMessage::find($data['messageId']);

                $this->conversationMessages->prepend($message);

                if ($message->receiverable_id === auth()->id() && $message->receiverable_type === auth()->user()::class) {
                    $message->last_read_at = now();
                    $message->save();

                    broadcast(new FilaChatMessageReadEvent($this->selectedConversation->id));
                }
            } else {
                // This will be executed to the sender if the receiver is not in the selected conversation
                if ($data['senderId'] === auth()->id()) {
                    broadcast(new FilaChatMessageReceiverIsAwayEvent($data['conversationId']));
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
        $this->conversationMessages->push(...$this->paginator->getCollection());

        $this->currentPage = $this->currentPage + 1;

        $this->dispatch('chat-box-preserve-scroll-position');
    }

    #[Computed()]
    public function paginator()
    {
        return $this->selectedConversation->messages()->latest()->paginate(10, ['*'], 'page', $this->currentPage);
    }

    public function downloadFile(string $path, string $originalFileName)
    {
        // Check if the file exists
        if (Storage::disk(config('filachat.disk'))->exists($path)) {
            return Storage::disk(config('filachat.disk'))->download($path, $originalFileName);
        }

        return abort(404, 'File not found');
    }

    public function render()
    {
        return view('filachat::filachat.components.chat-box');
    }
}
