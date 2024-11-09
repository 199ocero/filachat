<?php

namespace JaOcero\FilaChat\Services;

use Exception;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use InvalidArgumentException;
use JaOcero\FilaChat\Events\FilaChatMessageEvent;
use JaOcero\FilaChat\Models\FilaChatConversation;
use JaOcero\FilaChat\Models\FilaChatGroup;
use JaOcero\FilaChat\Models\FilaChatMessage;
use JaOcero\FilaChat\Pages\FilaChat;

class ChatListService
{
    protected $isRoleEnabled;

    protected $isAgent;

    protected $userModelClass;

    protected $agentModelClass;

    protected $userSearchableColumns;

    protected $agentSearchableColumns;

    protected $userChatListDisplayColumn;

    protected $agentChatListDisplayColumn;

    public function __construct()
    {
        $this->isRoleEnabled = config('filachat.enable_roles');
        $this->isAgent = auth()->user()->isAgent();
        $this->userModelClass = config('filachat.user_model');
        $this->agentModelClass = config('filachat.agent_model');
        $this->userChatListDisplayColumn = config('filachat.user_chat_list_display_column');
        $this->agentChatListDisplayColumn = config('filachat.agent_chat_list_display_column');

        // Check if the user model class exists
        if (!class_exists($this->userModelClass)) {
            throw new InvalidArgumentException('User model class ' . $this->userModelClass . ' not found');
        }

        // Check if the agent model class exists
        if (!class_exists($this->agentModelClass)) {
            throw new InvalidArgumentException('Agent model class ' . $this->agentModelClass . ' not found');
        }

        // Validate that all specified columns exist in the user model
        foreach (config('filachat.user_searchable_columns') as $column) {
            $userTable = (new $this->userModelClass)->getTable();
            if (!Schema::hasColumn($userTable, $column)) {
                throw new InvalidArgumentException('Column ' . $column . ' not found in ' . $userTable);
            }
        }
        $this->userSearchableColumns = config('filachat.user_searchable_columns');

        // Validate that all specified columns exist in the agent model
        foreach (config('filachat.agent_searchable_columns') as $column) {
            $agentTable = (new $this->agentModelClass)->getTable();
            if (!Schema::hasColumn($agentTable, $column)) {
                throw new InvalidArgumentException('Column ' . $column . ' not found in ' . $agentTable);
            }
        }
        $this->agentSearchableColumns = config('filachat.agent_searchable_columns');
    }

    public function getSearchResults(string $search): Collection
    {
        $searchTerm = '%' . $search . '%';

        if ($this->isRoleEnabled) {

            $agentIds = $this->agentModelClass::getAllAgentIds();

            if ($this->isAgent) {
                return $this->userModelClass::query()
                    ->whereNotIn('id', $agentIds)
                    ->where(function ($query) use ($searchTerm) {
                        foreach ($this->userSearchableColumns as $column) {
                            $query->orWhere($column, 'like', $searchTerm);
                        }
                    })
                    ->select(
                        DB::raw("CONCAT('user_', id) as user_key"),
                        DB::raw("$this->userChatListDisplayColumn as user_value")
                    )
                    ->get()
                    ->pluck('user_value', 'user_key');
            }

            return $this->agentModelClass::query()
                ->whereIn('id', $agentIds)
                ->where(function ($query) use ($searchTerm) {
                    foreach ($this->agentSearchableColumns as $column) {
                        $query->orWhere($column, 'like', $searchTerm);
                    }
                })
                ->select(
                    DB::raw("CONCAT('agent_', id) as agent_key"),
                    DB::raw("$this->agentChatListDisplayColumn as agent_value")
                )
                ->get()
                ->pluck('agent_value', 'agent_key');
        } else {
            if ($this->userModelClass === $this->agentModelClass) {
                return $this->userModelClass::query()
                    ->whereNot('id', auth()->id())
                    ->where(function ($query) use ($searchTerm) {
                        foreach ($this->userSearchableColumns as $column) {
                            $query->orWhere($column, 'like', $searchTerm);
                        }
                    })
                    ->select(
                        DB::raw("CONCAT('user_', id) as user_key"),
                        DB::raw("$this->userChatListDisplayColumn as user_value")
                    )
                    ->get()
                    ->pluck('user_value', 'user_key');
            }

            $userModel = $this->userModelClass::query()
                ->whereNot('id', auth()->id())
                ->where(function ($query) use ($searchTerm) {
                    foreach ($this->userSearchableColumns as $column) {
                        $query->orWhere($column, 'like', $searchTerm);
                    }
                })
                ->select(
                    DB::raw("CONCAT('user_', id) as user_key"),
                    DB::raw("$this->userChatListDisplayColumn as user_value")
                )
                ->get()
                ->pluck('user_value', 'user_key');

            $agentModel = $this->agentModelClass::query()
                ->whereNot('id', auth()->id())
                ->where(function ($query) use ($searchTerm) {
                    foreach ($this->agentSearchableColumns as $column) {
                        $query->orWhere($column, 'like', $searchTerm);
                    }
                })
                ->select(
                    DB::raw("CONCAT('agent_', id) as agent_key"),
                    DB::raw("$this->agentChatListDisplayColumn as agent_value")
                )
                ->get()
                ->pluck('agent_value', 'agent_key');

            return $userModel->merge($agentModel);
        }
    }

    public function getOptionLabel(string $value): ?string
    {
        if (preg_match('/^user_(\d+)$/', $value, $matches)) {
            $id = (int)$matches[1];

            return $this->userModelClass::find($id)->{$this->userChatListDisplayColumn};
        }

        if (preg_match('/^agent_(\d+)$/', $value, $matches)) {
            $id = (int)$matches[1];

            return $this->agentModelClass::find($id)->{$this->agentChatListDisplayColumn};
        }

        return null;
    }

    public function createConversation(array $data): void
    {
        try {
            DB::transaction(function () use ($data) {

                $senderableId = auth()->id();
                $senderableType = auth()->user()::class;

                if ($data['type'] === 'group') {

                    $group = FilaChatGroup::query()->create([
                        'created_by' => $senderableId,
                        'owner_id' => $senderableId,
                        'name' => $data['group_name'],
                    ]);

                    foreach ($data['receiverable_id'] as $receiverableId) {
                        $group->members()->create([
                            'member_id' => Str::replace('agent_', '', Str::replace('user_', '', $receiverableId)),
                        ]);
                    }

                    $receiverableId = $group->id;
                    $receiverableType = FilaChatGroup::class;
                    $conversation = FilaChatConversation::query()->create([
                        'senderable_id' => $senderableId,
                        'senderable_type' => auth()->user()::class,
                        'receiverable_id' => $receiverableId,
                        'receiverable_type' => $receiverableType,
                    ]);
                } else {
                    $receiverableId = $data['receiverable_id'];

                    if (preg_match('/^user_(\d+)$/', $receiverableId, $matches)) {
                        $receiverableType = $this->userModelClass;
                        $receiverableId = (int)$matches[1];
                    }

                    if (preg_match('/^agent_(\d+)$/', $receiverableId, $matches)) {
                        $receiverableType = $this->agentModelClass;
                        $receiverableId = (int)$matches[1];
                    }
                    $foundConversation = FilaChatConversation::query()
                        ->where(function ($query) use ($receiverableId, $receiverableType, $senderableId, $senderableType) {
                            $query->where(function ($query) use ($senderableId, $senderableType) {
                                $query->where('senderable_id', $senderableId)
                                    ->where('senderable_type', $senderableType);
                            })
                                ->orWhere(function ($query) use ($receiverableId, $receiverableType) {
                                    $query->where('senderable_id', $receiverableId)
                                        ->where('senderable_type', $receiverableType);
                                });
                        })
                        ->where(function ($query) use ($receiverableId, $receiverableType, $senderableId, $senderableType) {
                            $query->where(function ($query) use ($receiverableId, $receiverableType) {
                                $query->where('receiverable_id', $receiverableId)
                                    ->where('receiverable_type', $receiverableType);
                            })
                                ->orWhere(function ($query) use ($senderableId, $senderableType) {
                                    $query->where('receiverable_id', $senderableId)
                                        ->where('receiverable_type', $senderableType);
                                });
                        })
                        ->first();
                    if (!$foundConversation) {
                        $conversation = FilaChatConversation::query()->create([
                            'senderable_id' => $senderableId,
                            'senderable_type' => $senderableType,
                            'receiverable_id' => $receiverableId,
                            'receiverable_type' => $receiverableType,
                        ]);
                    } else {
                        $conversation = $foundConversation;
                    }
                }

                $message = FilaChatMessage::query()->create([
                    'filachat_conversation_id' => $conversation->id,
                    'senderable_id' => $senderableId,
                    'senderable_type' => $senderableType,
                    'receiverable_id' => $receiverableId,
                    'receiverable_type' => $receiverableType,
                    'message' => $data['message'],
                ]);

                $conversation->updated_at = now();

                $conversation->save();

                if ($data['type'] === 'group') {
                    foreach ($group->members as $member) {
                        broadcast(new FilaChatMessageEvent(
                            $conversation->id,
                            $message->id,
                            $member->id,
                            $senderableId,
                        ));
                    }
                } else {
                    broadcast(new FilaChatMessageEvent(
                        $conversation->id,
                        $message->id,
                        $receiverableId,
                        $senderableId,
                    ));
                }

                return redirect(FilaChat::getUrl(tenant: filament()->getTenant()) . '/' . $conversation->id);
            });
        } catch (Exception $exception) {
            Notification::make()
                ->title('Something went wrong')
                ->body($exception->getMessage())
                ->danger()
                ->persistent()
                ->send();
        }
    }

    public static function make(): self
    {
        return new self;
    }
}
