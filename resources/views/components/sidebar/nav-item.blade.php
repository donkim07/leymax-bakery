@props(['route', 'icon'])

@php
$isActive = request()->routeIs($route);
@endphp

<a href="{{ route($route) }}" 
   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-150 {{ $isActive ? 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-100' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-gray-100' }}">
    @svg('heroicon-o-'.$icon, 'mr-3 flex-shrink-0 h-6 w-6 '.($isActive ? 'text-purple-500 dark:text-purple-400' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400 dark:group-hover:text-gray-300'))
    {{ $slot }}
</a> 