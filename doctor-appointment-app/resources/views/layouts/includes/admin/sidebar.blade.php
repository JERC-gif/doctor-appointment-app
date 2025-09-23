@php
// Arreglo de íconos
$links = [
    [
        'name' => 'Dashboard',
        'icon' => 'fa-solid fa-gauge',
        'href' => route('admin.dashboard'),
        'active' => request()->routeIs('admin.dashboard'),
    ],
];
@endphp

<aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
        <ul class="space-y-2 font-medium">
            @foreach ($links as $link)
                <li>
                    <a href="{{ $link['href'] }}"
                       class="flex items-center p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700
                              {{ $link['active'] ? 'bg-gray-200 dark:bg-gray-600' : '' }}">
                        <i class="{{ $link['icon'] }} w-5 h-5 text-gray-500 dark:text-gray-400"></i>
                        <span class="ml-3">{{ $link['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</aside>
