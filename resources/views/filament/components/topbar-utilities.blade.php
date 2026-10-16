@php
    $locales = config('app.supported_locales', []);
    $currentLocale = app()->getLocale();
    $isRtl = data_get($locales, $currentLocale . '.rtl', false);

    $flagFor = fn (string $code) => file_exists(public_path("flags/{$code}.svg"))
        ? '<img src="'.e(asset("flags/{$code}.svg")).'" alt="" class="w-4 h-4 rounded-full object-cover" />'
        : '';
@endphp

<div
    class="flex items-center gap-1 sm:gap-1.5"
    x-data="{
        langOpen: false,
        isFullscreen: false,
        toggleTheme() {
            const isDark = document.documentElement.classList.contains('dark');
            const next = isDark ? 'light' : 'dark';
            window.dispatchEvent(new CustomEvent('theme-changed', { detail: next }));
        },
        toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen?.();
                this.isFullscreen = true;
            } else {
                document.exitFullscreen?.();
                this.isFullscreen = false;
            }
        }
    }"
>
    {{-- 1. Settings Gear Icon --}}
    <a
        href="{{ url('/admin/settings/manage-branding') }}"
        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800 transition"
        title="Settings"
    >
        <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
    </a>

    {{-- 2. Light / Dark Mode Sun Toggle --}}
    <button
        type="button"
        @click="toggleTheme()"
        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800 transition"
        title="Toggle Theme"
    >
        <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
    </button>

    <div class="aura-lang-switch" title="{{ __('Language') }}">
        @foreach($locales as $code => $locale)
            <a
                href="{{ request()->fullUrlWithQuery(['lang' => $code]) }}"
                class="{{ $currentLocale === $code ? 'is-active' : '' }}"
                lang="{{ $code }}"
            >
                {!! $flagFor($code) !!}
                <span>{{ $code === 'id' ? 'ID' : 'EN' }}</span>
            </a>
        @endforeach
    </div>

    {{-- 4. Fullscreen Toggle Icon --}}
    <button
        type="button"
        @click="toggleFullscreen()"
        class="hidden sm:inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800 transition"
        title="Toggle Fullscreen"
    >
        <svg class="w-[17px] h-[17px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4h4M20 8V4h-4M4 16v4h4m12-4v4h-4"/>
        </svg>
    </button>
</div>
