<?php

namespace JaOcero\FilaChat\Livewire;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use JaOcero\FilaChat\Models\FilaChatMessage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class SearchConversation extends Component
{
    public $search = '';

    public $currentPage = 1;

    public Collection $messages;

    public function mount(): void
    {
        $this->messages = collect();
    }

    #[On('close-modal')]
    public function clearSearch(): void
    {
        $this->search = '';
        $this->currentPage = 1;
        $this->messages = collect();
        $this->loadMessages();
    }

    public function loadMessages(): void
    {
        $this->messages->push(...$this->paginator->getCollection());

        $this->currentPage = $this->currentPage + 1;
    }

    public function loadMoreMessages(): void
    {
        $this->loadMessages();
    }

    #[Computed()]
    public function paginator(): \Illuminate\Contracts\Pagination\LengthAwarePaginator|LengthAwarePaginator
    {
        $searchTerm = trim($this->search);

        $messages = new LengthAwarePaginator([], 0, 10, $this->currentPage);

        if (! empty($searchTerm)) {
            $messages = FilaChatMessage::query()
                ->with(['conversation', 'senderable', 'receiverable'])
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('senderable_id', auth()->id())
                            ->where('senderable_type', auth()->user()::class);
                    })
                        ->orWhere(function ($query) {
                            $query->where('receiverable_id', auth()->id())
                                ->where('receiverable_type', auth()->user()::class);
                        });
                })
                ->where('message', 'like', '%' . $searchTerm . '%')
                ->latest()
                ->paginate(10, ['*'], 'page', $this->currentPage);
        }

        return $messages;
    }

    public function updatedSearch(): void
    {
        $this->currentPage = 1;
        $this->messages = collect();
        $this->loadMessages();
    }

    public function render()
    {
        return view('filachat::filachat.components.search-conversation', [
            'messages' => $this->messages,
        ]);
    }
}
