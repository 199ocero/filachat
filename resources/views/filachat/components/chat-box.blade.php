@props(['selectedConversation'])
<!-- Right Section (Chat Conversation) -->
<div class="flex flex-col w-full md:w-2/3 overflow-hidden">
    @if ($selectedConversation)
        <!-- Chat Header -->
        <div class="flex items-center h-20 gap-2 p-5 border-b dark:border-gray-800/60 border-gray-200/90">
            <x-filament::avatar
                src="https://ui-avatars.com/api/?name={{ urlencode($selectedConversation->other_person_name) }}"
                alt="Profile" size="lg" />
            <div class="flex flex-col">
                <p class="text-base font-bold">{{ $selectedConversation->other_person_name }}</p>
                @php
                    if (auth()->id() === $selectedConversation->receiverable_id) {
                        $isOtherPersonAgent = $selectedConversation->senderable->isAgent();
                    } else {
                        $isOtherPersonAgent = $selectedConversation->receiverable->isAgent();
                    }
                @endphp
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    @if ($isOtherPersonAgent)
                        Agent
                    @else
                        User
                    @endif
                </p>
            </div>
        </div>

        <!-- Chat Messages -->
        <div x-data="{ markAsRead: false }" x-init="Echo.channel('filachat')
            .listen('.JaOcero\\FilaChat\\Events\\FilaChatMessageReadEvent', e => {
                if (e.conversationId == @js($selectedConversation->id)) {
                    markAsRead = true;
                }
            });" id="chatContainer"
            class="flex flex-col-reverse flex-1 p-5 overflow-y-auto">
            <!-- Message Item -->
            @foreach ($messages as $index => $message)
                <div wire:key="{{ $message->id }}">
                  @php
                        $nextMessage = $messages[$index + 1] ?? null;
                        $nextMessageDate = $nextMessage ? \Carbon\Carbon::parse($nextMessage->created_at)->setTimezone(config('filachat.timezone', 'app.timezone'))->format('Y-m-d') : null;
                        $currentMessageDate = \Carbon\Carbon::parse($message->created_at)->setTimezone(config('filachat.timezone', 'app.timezone'))->format('Y-m-d');

                        // Show date badge if the current message is the last one of the day
                        $showDateBadge = $currentMessageDate !== $nextMessageDate;
                    @endphp

                    @if ($showDateBadge)
                        <div class="flex justify-center">
                            <x-filament::badge>
                                {{ \Carbon\Carbon::parse($message->created_at)->setTimezone(config('filachat.timezone', 'app.timezone'))->format('F j, Y') }}
                            </x-filament::badge>
                        </div>
                    @endif
                    @if ($message->senderable_id !== auth()->user()->id)
                        @php
                            $previousMessageDate = isset($messages[$index - 1]) ? \Carbon\Carbon::parse($messages[$index - 1]->created_at)->setTimezone(config('filachat.timezone', 'app.timezone'))->format('Y-m-d') : null;

                            $currentMessageDate = \Carbon\Carbon::parse($message->created_at)->setTimezone(config('filachat.timezone', 'app.timezone'))->format('Y-m-d');

                            $previousSenderId = $messages[$index - 1]->senderable_id ?? null;

                            // Show avatar if the current message is the first in a consecutive sequence or a new day
                            $showAvatar = $message->senderable_id !== auth()->user()->id && ($message->senderable_id !== $previousSenderId || $currentMessageDate !== $previousMessageDate);
                        @endphp
                        <!-- Left Side -->
                        <div class="flex items-end gap-2 mb-2">
                            @if ($showAvatar)
                                <x-filament::avatar
                                    src="https://ui-avatars.com/api/?name={{ urlencode($selectedConversation->other_person_name) }}"
                                    alt="Profile" size="sm" />
                            @else
                                <div class="w-6 h-6"></div> <!-- Placeholder to align the messages properly -->
                            @endif
                            <div class="max-w-md p-2 bg-gray-200 rounded-lg dark:bg-gray-800">
                                <p class="text-sm">{{ $message->message }}</p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-600 text-start">
                                    {{ \Carbon\Carbon::parse($message->created_at)->setTimezone(config('filachat.timezone', 'app.timezone'))->format('F j, Y') }}
                                </p>
                            </div>
                        </div>
                    @else
                        <!-- Right Side -->
                        <div class="flex flex-col items-end gap-2 mb-2">
                            <div class="max-w-md p-2 text-white rounded-lg bg-primary-600 dark:bg-primary-500">
                                <p class="text-sm">{{ $message->message }}</p>
                                <p class="text-xs text-primary-300 dark:text-primary-200 text-end">
                                    {{ \Carbon\Carbon::parse($message->created_at)->setTimezone(config('filachat.timezone', 'app.timezone'))->format('F j, Y') }}
                                </p>
                            </div>
                            <template x-if="markAsRead || @js($message->last_read_at) !== null">
                                <p class="text-xs text-gray-600 dark:text-primary-200 text-end">
                                    Seen at
                                    @php
                                        $lastReadAt = \Carbon\Carbon::parse($message->last_read_at)->setTimezone(config('filachat.timezone', 'app.timezone'));

                                        if ($lastReadAt->isToday()) {
                                            $date = $lastReadAt->format('g:i A');
                                        } else {
                                            $date = $lastReadAt->format('M d, Y g:i A');
                                        }
                                    @endphp
                                    {{ $date }}
                                </p>
                            </template>
                        </div>
                    @endif
                </div>
            @endforeach
            <!-- Repeat Message Item for multiple messages -->
            @if ($this->paginator->hasMorePages())
                <div x-intersect="$wire.loadMoreMessages" class="h-4">
                    <div class="w-full mb-2 text-center text-gray-500">Loading more messages...</div>
                </div>
            @endif
        </div>



        <!-- Chat Input -->
        <div class="w-full p-4 border-t dark:border-gray-800/60 border-gray-200/90">
            <form wire:submit="sendMessage" class="flex items-center justify-between w-full gap-2">
                <div class="w-full">
                    {{ $this->form }}
                </div>

                <x-filament::button type="submit">
                    Send
                </x-filament::button>
            </form>

            <x-filament-actions::modals />
        </div>
    @else
        <div class="flex flex-col items-center justify-center h-full p-3">
            <div class="p-3 mb-4 bg-gray-100 rounded-full dark:bg-gray-500/20">
                <x-filament::icon icon="heroicon-m-x-mark" class="w-6 h-6 text-gray-500 dark:text-gray-400" />
            </div>
            <p class="text-base text-center text-gray-600 dark:text-gray-400">
                No selected conversation
            </p>
        </div>
    @endif

</div>
@script
    <script>
        $wire.on('chat-box-scroll-to-bottom', () => {

            chatContainer = document.getElementById('chatContainer');
            chatContainer.scrollTo({
                top: chatContainer.scrollHeight,
                behavior: 'smooth',
            });

            setTimeout(() => {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }, 400);
        });
    </script>
@endscript
