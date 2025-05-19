@props(['title', 'icon'])

<div x-data="{ open: false }" class="space-y-1">
    <!-- Group Header -->
    <button @click="open = !open" class="w-full group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-gray-100">
        <x-dynamic-component :component="'heroicon-o-' . $icon" class="mr-3 flex-shrink-0 h-6 w-6 text-gray-400 group-hover:text-gray-500 dark:text-gray-400 dark:group-hover:text-gray-300"/>
        <span class="flex-1">{{ $title }}</span>
        <x-heroicon-o-chevron-down x-bind:class="{ 'rotate-180': open }" class="ml-3 flex-shrink-0 h-5 w-5 transform transition-transform duration-150"/>
    </button>

    <!-- Group Content -->
    <div x-show="open" x-collapse class="space-y-1 pl-11">
        {{ $slot }}
    </div>
</div> 