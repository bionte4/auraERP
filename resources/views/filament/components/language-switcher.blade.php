@php
    $locales = config('app.supported_locales', []);
    $currentLocale = app()->getLocale();
    $isRtl = data_get($locales, $currentLocale.'.rtl', false);
    $compact = (bool) ($compact ?? false);

    $flagFor = fn (string $code) => file_exists(public_path("flags/{$code}.svg"))
        ? '<img src="'.e(asset("flags/{$code}.svg")).'" alt="" class="'.($compact ? 'w-4 h-4' : 'w-5 h-5').' rounded-sm object-cover" />'
        : '';

    $codeLabel = strtoupper(str_contains($currentLocale, '_')
        ? strstr($currentLocale, '_', true)
        : $currentLocale);
@endphp

<div @class([
    'aura-lang-switcher',
    'aura-lang-switcher--compact' => $compact,
    'flex items-center gap-1' => ! $compact,
]) x-data="{ open: false }">
    <div class="relative">
        <button
            @click="open = !open"
            @click.outside="open = false"
            type="button"
            @class([
                'flex items-center font-medium transition',
                'gap-1.5 rounded-full border border-slate-200 bg-white px-2.5 py-1.5 text-xs text-slate-600 hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800' => $compact,
                'gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800' => ! $compact,
            ])
            aria-label="{{ str_starts_with($currentLocale, 'id') ? 'Pilih bahasa' : 'Choose language' }}"
        >
            {!! $flagFor($currentLocale) !!}
            @if ($compact)
                <span>{{ $codeLabel }}</span>
            @else
                <span class="hidden sm:inline">{{ data_get($locales, $currentLocale.'.native', strtoupper($currentLocale)) }}</span>
            @endif
            <svg class="{{ $compact ? 'w-3.5 h-3.5' : 'w-4 h-4' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            @class([
                'absolute mt-1.5 origin-top-right rounded-lg bg-white shadow-lg ring-1 ring-black/5 dark:bg-gray-800 dark:ring-white/10 z-50',
                $isRtl ? 'left-0' : 'right-0',
                $compact ? 'w-40' : 'w-52',
            ])
            style="display: none;"
        >
            <div class="py-1">
                @foreach ($locales as $code => $locale)
                    <a
                        href="{{ request()->fullUrlWithQuery(['lang' => $code]) }}"
                        @class([
                            'flex items-center gap-2.5 px-3 py-2 text-sm',
                            $currentLocale === $code
                                ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/50 dark:text-primary-400'
                                : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700',
                        ])
                    >
                        {!! $flagFor($code) !!}
                        <span>{{ $locale['native'] ?? strtoupper($code) }}</span>
                        @if ($currentLocale === $code)
                            <svg class="w-4 h-4 {{ $isRtl ? 'mr-auto' : 'ml-auto' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
