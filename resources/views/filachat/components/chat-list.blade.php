@php
    use JaOcero\FilaChat\Pages\FilaChat;
@endphp

@props(['selectedConversation'])
<div class="flex flex-col w-16 md:w-1/3 border-r border-gray-200 dark:border-gray-800/50">
    <div class="flex items-center h-20 gap-2 px-5 border-b dark:border-gray-800/60 border-gray-200/90">
        <p class="text-lg font-bold hidden md:flex">Active Conversations</p>
        <x-filament::badge>
            {{ $this->conversations->count() }}
        </x-filament::badge>
    </div>

    <!-- Create Conversation -->
    <div class="hidden md:block px-5 pt-5 w-full">
        {{ $this->createConversationMediumSizeAction }}
    </div>
    <div class="md:hidden block p-1 w-full">
        {{ $this->createConversationSmallSizeAction }}
    </div>

    <!-- Search Bar -->
    <div class="hidden md:block sticky top-0 z-10 h-20 p-5 border-b dark:border-gray-800/60 border-gray-200/90">
        <x-filament::input.wrapper suffix-icon="heroicon-m-magnifying-glass">
            <x-filament::input type="text" placeholder="Search any messages..." x-on:click="$dispatch('open-modal', { id: 'search-conversation' })"/>
        </x-filament::input.wrapper>
    </div>
     <div class="md:hidden block sticky top-0 z-10 p-1 border-b dark:border-gray-800/60 border-gray-200/90">
            <x-filament::button class="w-full" color="gray" icon="heroicon-m-magnifying-glass" x-on:click="$dispatch('open-modal', { id: 'search-conversation' })"></x-filament::button>
    </div>
    <!-- Search Modal -->
    <livewire:filachat-search-conversation/>

    <!-- Conversations -->
    <div class="flex-1 overflow-y-auto">
        @if ($this->conversations->count() > 0)
            <div x-init="Echo.channel('filachat')
                .listen('FilaChatMessageEvent', e => {
                    Livewire.dispatch('load-conversations');
                });" class="grid w-full">
                @foreach ($this->conversations as $conversation)
                    <a wire:key="{{ $conversation->id }}" wire:navigate
                        href="{{ FilaChat::getUrl(tenant: filament()->getTenant()) . '/' . $conversation->id }}"
                        @class([
                            'p-2 md:p-5 mx-1 my-0.5 rounded-xl',
                            'hover:bg-gray-100 hover:dark:bg-gray-800/20' =>
                                $conversation->id != $selectedConversation?->id,
                            'bg-gray-200/60 dark:bg-gray-800' =>
                                $conversation->id == $selectedConversation?->id,
                        ])>
                        <div class="flex items-start justify-start w-full gap-2">
                            <x-filament::avatar
                                src="https://ui-avatars.com/api/?name={{ urlencode($conversation->other_person_name) }}"
                                alt="Profile" size="lg" />
                            <div class="hidden md:grid w-full grid-cols-1">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-semibold truncate">{{ $conversation->other_person_name }}
                                    </p>
                                    <p class="text-sm font-light text-gray-600 dark:text-gray-500">
                                        {{ $conversation->last_message_time }}
                                    </p>
                                </div>
                                <div class="flex items-center justify-between gap-1">
                                    <p class="text-sm text-gray-600 truncate dark:text-gray-400">
                                        {{ $conversation->latest_message }}</p>
                                    @if ($conversation->unread_count > 0)
                                        <x-filament::badge>
                                            {{ $conversation->unread_count }}
                                        </x-filament::badge>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center h-full">
                <div class="p-3 mb-4 bg-gray-100 rounded-full dark:bg-gray-500/20">
                    <x-filament::icon icon="heroicon-m-x-mark" class="w-6 h-6 text-gray-500 dark:text-gray-400" />
                </div>
                <p class="text-base text-gray-600 dark:text-gray-400">
                    No conversations yet
                </p>
            </div>
        @endif
    </div>
    <x-filament-actions::modals />
</div>
