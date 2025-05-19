<div>
    <div class="divide-y divide-gray-100 dark:divide-gray-700">
        <div class="p-4">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-medium text-gray-900 dark:text-white">
                    Notifications
                    @if($unreadCount > 0)
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </h2>
                @if(count($notifications) > 0)
                    <button wire:click="markAllAsRead" type="button"
                        class="text-sm font-medium text-purple-600 hover:text-purple-500 dark:text-purple-400 dark:hover:text-purple-300">
                        Mark all as read
                    </button>
                @endif
            </div>
        </div>

        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications as $notification)
                <div wire:key="notification-{{ $notification['id'] }}"
                    class="relative p-4 hover:bg-gray-50 dark:hover:bg-gray-700 {{ !$notification['read'] ? 'bg-purple-50 dark:bg-purple-900/10' : '' }}">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 pt-0.5">
                            @if(str_contains($notification['type'], 'StockAlert'))
                                <svg class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            @elseif(str_contains($notification['type'], 'OrderReceived'))
                                <svg class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            @else
                                <svg class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            @endif
                        </div>
                        <div class="ml-3 w-0 flex-1">
                            <a href="{{ $notification['action_url'] }}" class="block">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $notification['title'] }}
                                </p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $notification['message'] }}
                                </p>
                                <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                                    {{ $notification['time'] }}
                                </p>
                            </a>
                        </div>
                        @if(!$notification['read'])
                            <div class="ml-4 flex-shrink-0">
                                <button wire:click="markAsRead('{{ $notification['id'] }}')"
                                    class="inline-flex text-sm text-purple-600 hover:text-purple-500 dark:text-purple-400 dark:hover:text-purple-300">
                                    Mark as read
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-14 px-6 text-center text-sm sm:px-14">
                    <svg class="mx-auto h-6 w-6 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <p class="mt-4 font-semibold text-gray-900 dark:text-white">No notifications</p>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">You're all caught up! Check back later for new notifications.</p>
                </div>
            @endforelse
        </div>
    </div>
</div> 