@props([
  'type' => 'button',
])

<button
  type="{{ $type }}"
  {{ $attributes->merge([
    'class' => 'inline-flex items-center gap-2 rounded-lg px-4 py-2
                bg-indigo-600 text-white hover:bg-indigo-700
                disabled:opacity-50 disabled:cursor-not-allowed transition'
  ]) }}
>
  {{ $slot }}
</button>

