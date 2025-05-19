<div>
    <div class="relative">
        <input type="text" wire:model.debounce.300ms="query"
            class="block w-full rounded-md border-gray-300 dark:border-gray-700 pl-10 pr-3 py-2 text-sm placeholder-gray-500 dark:placeholder-gray-400 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500 dark:bg-gray-700 dark:text-white"
            placeholder="Search across all modules...">
        <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
            <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
            </svg>
        </div>
    </div>

    @if(strlen($query) >= 2)
        <div class="absolute mt-2 w-96 rounded-md bg-white dark:bg-gray-800 shadow-lg">
            @if(count($results) > 0)
                <ul class="max-h-96 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($results as $result)
                        <li>
                            <a href="{{ $result['url'] }}" class="block hover:bg-gray-50 dark:hover:bg-gray-700">
                                <div class="px-4 py-4 sm:px-6">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            {!! $result['icon'] !!}
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $result['title'] }}
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $result['description'] }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="py-14 px-6 text-center text-sm sm:px-14">
                    <svg class="mx-auto h-6 w-6 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="mt-4 font-semibold text-gray-900 dark:text-white">No results found</p>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">We couldn't find anything with that term. Please try again.</p>
                </div>
            @endif
        </div>
    @endif
</div> 