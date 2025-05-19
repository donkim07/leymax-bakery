php
<div x-data="{ open: false, query: '' }" @click.away="open = false">
    <div class="relative">
        <button type="button" @click="open = !open"
            class="flex items-center rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm leading-4 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
            <svg class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
            </svg>
            <span>Search</span>
            <svg class="ml-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>

        <div x-show="open" x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute right-0 z-10 mt-2 w-96 origin-top-right rounded-md bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
            <div class="p-4">
                <div class="relative">
                    <input type="text" x-model="query" @keyup.debounce.300ms="$wire.search(query)"
                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 pl-10 pr-3 py-2 text-sm placeholder-gray-500 dark:placeholder-gray-400 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500 dark:bg-gray-700 dark:text-white"
                        placeholder="Search across all modules...">
                    <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>

                <div class="mt-4 max-h-96 overflow-y-auto" x-show="query.length > 0">
                    <template x-if="$wire.results && $wire.results.length > 0">
                        <div class="divide-y divide-gray-100 dark:divide-gray-700">
                            <template x-for="result in $wire.results" :key="result.id">
                                <a :href="result.url"
                                    class="flex items-center px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-gray-400 dark:text-gray-500" x-html="result.icon"></svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="result.title"></p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="result.description"></p>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </template>
                    <template x-if="query.length > 0 && (!$wire.results || $wire.results.length === 0)">
                        <div class="py-14 px-6 text-center text-sm sm:px-14">
                            <svg class="mx-auto h-6 w-6 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="mt-4 font-semibold text-gray-900 dark:text-white">No results found</p>
                            <p class="mt-2 text-gray-500 dark:text-gray-400">We couldn't find anything with that term. Please try again.</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
