<div x-data="{ open: false }" class="relative">
    <div class="flex items-center space-x-8">
        @php
            $businesses = [
                'bakery' => ['name' => 'Bakery Shop', 'icon' => 'cake'],
                'tools' => ['name' => 'Cake Tools Shop', 'icon' => 'tools'],
                'academy' => ['name' => 'Leymax Academy', 'icon' => 'academic-cap']
            ];
            $currentBusiness = auth()->user()->current_business ?? 'bakery';
        @endphp

        @foreach($businesses as $key => $business)
            @if(in_array($key, auth()->user()->enabled_businesses ?? []))
                <button type="button"
                    class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium transition-colors duration-150 ease-in-out {{ $currentBusiness === $key ? 'bg-purple-100 text-purple-900 dark:bg-purple-900 dark:text-purple-100' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white' }}"
                    onclick="window.location.href='{{ route('switch.business', $key) }}'">
                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        @switch($business['icon'])
                            @case('cake')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z" />
                                @break
                            @case('tools')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                @break
                            @case('academic-cap')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                @break
                        @endswitch
                    </svg>
                    <span>{{ $business['name'] }}</span>
                </button>
            @endif
        @endforeach
    </div>
</div> 