@php
    use JaOcero\FilaChat\Pages\FilaChat;
@endphp

<x-filament::modal width="xl" id="search-conversation">
    <x-slot name="heading">
        {{__('Search Messages in your Conversations')}}
    </x-slot>

    <x-filament::input.wrapper suffix-icon="heroicon-m-magnifying-glass">
        <x-filament::input type="search" placeholder="{{__('Search any messages...')}}" wire:model.live.debounce.500ms="search"/>
    </x-filament::input.wrapper>

    {{-- Dropdown results --}}
    @if(count($messages) > 0)
        <div class="relative">
            <ul class="absolute z-10 -mt-2 w-full bg-white border divide-y dark:divide-gray-800 border-gray-200 rounded-lg shadow dark:border-gray-800 dark:bg-gray-900 max-h-64 overflow-y-auto">
                @foreach($messages as $message)
                    <li wire:key="{{ $message->id }}">
                        <a wire:navigate href="{{ FilaChat::getUrl(tenant: filament()->getTenant()) . '/' . $message->conversation->id }}">
                            <div class="m-1">
                                <div class="flex items-center rounded-lg gap-2 w-full p-3 hover:bg-gray-100 dark:hover:bg-white/5">
                                    <x-filament::avatar
                                        src="https://ui-avatars.com/api/?name={{ urlencode($message->other_person_name) }}"
                                        alt="Profile" size="lg" />
                                    <div class="flex flex-col w-0 flex-1">
                                        <div class="flex flex-col-reverse sm:flex-row items-start sm:items-center sm:justify-between">
                                            <p class="text-sm font-semibold">{{ $message->other_person_name }}</p>
                                            <p class="text-xs">{{ \Carbon\Carbon::parse($message->created_at)->setTimezone(config('filachat.timezone', 'app.timezone'))->format('F j, Y') }}</p>
                                        </div>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 truncate ">
                                            {{$message->message}}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                @endforeach
                <!-- Repeat Message Item for multiple messages -->
                @if ($this->paginator->hasMorePages())
                    <div x-intersect="$wire.loadMoreMessages" class="h-4">
                        <p class="w-full text-center text-gray-500">{{__('Loading more messages...')}}</p>
                    </div>
                @endif
            </ul>
        </div>
    @elseif(!empty($search))
        <div class="relative">
           <div class="absolute z-10 w-full bg-white border dark:divide-gray-800 border-gray-200 rounded-lg shadow dark:border-gray-800 dark:bg-gray-900 max-h-64 overflow-y-auto">
                <p class="w-full p-3 text-sm text-center text-gray-500 dark:text-gray-400">
                    {{__('No results found.')}}
                </p>
            </div>
        </div>
    @endif
</x-filament::modal>
