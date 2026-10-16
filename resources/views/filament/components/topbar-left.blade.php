{{-- Left Topbar Quick Search & Mega Menu Trigger (Dispatchanel / Dhonu Style) --}}
<div class="hidden md:flex items-center gap-4 mr-auto pl-2" x-data="{ megaOpen: false, quickOpen: false }">
    {{-- Quick Search Input Group with Primary Blue Button --}}
    <div class="flex items-center">
        <button
            type="button"
            x-on:click="$dispatch('open-quick-navigation')"
            class="group flex items-center justify-between w-52 lg:w-64 h-9 pl-3.5 pr-2 text-xs text-slate-400 dark:text-slate-400 bg-slate-50/90 dark:bg-slate-800/80 border border-r-0 border-slate-200/90 dark:border-slate-700 rounded-l-lg hover:border-indigo-300 dark:hover:border-indigo-500/60 transition"
        >
            <span>Quick Search...</span>
            <kbd class="hidden sm:inline-block px-1.5 py-0.5 text-[10px] font-semibold text-slate-400 bg-white dark:bg-slate-900 rounded border border-slate-200 dark:border-slate-700">⌘K</kbd>
        </button>
        <button
            type="button"
            x-on:click="$dispatch('open-quick-navigation')"
            class="inline-flex items-center justify-center h-9 w-9 rounded-r-lg text-white shadow-xs transition hover:opacity-95"
            style="background: linear-gradient(135deg, #4f67f0 0%, #3b52d9 100%);"
            title="Quick Search (⌘K)"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </button>
    </div>

    {{-- Quick Actions ("Shortcuts" Dropdown) --}}
    <div class="relative" @click.outside="quickOpen = false">
        <button
            type="button"
            @click="quickOpen = !quickOpen; megaOpen = false"
            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-md hover:bg-slate-100/70 dark:hover:bg-slate-800 transition"
        >
            <span>Quick Box</span>
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div
            x-show="quickOpen"
            x-transition
            style="display: none;"
            class="absolute left-0 mt-2 w-56 rounded-xl bg-white dark:bg-slate-900 shadow-lg border border-slate-200/90 dark:border-slate-800 py-2 z-50"
        >
            <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                Quick Launch
            </div>
            @if (\Webkul\Support\Filament\Pages\WelcomeDashboard::canAccess())
                <a href="{{ url('/admin/welcome') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
                    <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                    Welcome Overview
                </a>
            @endif
            <a href="{{ url('/admin/performance-evaluation-dashboard') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                Evaluation Dashboard
            </a>
            <a href="{{ url('/admin/absen') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
                <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                Live GPS Clock-In
            </a>
            <a href="{{ url('/admin/pos/terminal') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
                <span class="h-2 w-2 rounded-full bg-sky-500"></span>
                POS Terminal
            </a>
        </div>
    </div>

    {{-- Mega Menu Dropdown --}}
    <div class="relative" @click.outside="megaOpen = false">
        <button
            type="button"
            @click="megaOpen = !megaOpen; quickOpen = false"
            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-md hover:bg-slate-100/70 dark:hover:bg-slate-800 transition"
        >
            <span>Mega Menu</span>
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div
            x-show="megaOpen"
            x-transition
            style="display: none;"
            class="absolute left-0 mt-2 w-[460px] rounded-xl bg-white dark:bg-slate-900 shadow-xl border border-slate-200/90 dark:border-slate-800 p-4 z-50 grid grid-cols-2 gap-4"
        >
            <div>
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">HR & Operations</div>
                <ul class="space-y-1.5 text-xs">
                    <li><a href="{{ url('/admin/employees/employees') }}" class="block px-2 py-1.5 rounded-md text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Employees Directory</a></li>
                    <li><a href="{{ url('/admin/evaluations') }}" class="block px-2 py-1.5 rounded-md text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Quarterly Evaluations (PKWT)</a></li>
                    <li><a href="{{ url('/admin/absen') }}" class="block px-2 py-1.5 rounded-md text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Attendance & Geofence</a></li>
                    <li><a href="{{ url('/admin/time-off') }}" class="block px-2 py-1.5 rounded-md text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Time Off & Leave</a></li>
                </ul>
            </div>
            <div>
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Business & System</div>
                <ul class="space-y-1.5 text-xs">
                    @if (\Webkul\Support\Filament\Pages\TeamBoard::canAccess())
                        <li><a href="{{ url('/admin/team-board') }}" class="block px-2 py-1.5 rounded-md text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Team Board</a></li>
                    @endif
                    <li><a href="{{ url('/admin/project') }}" class="block px-2 py-1.5 rounded-md text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Projects & Tasks</a></li>
                    <li><a href="{{ url('/admin/pos/terminal') }}" class="block px-2 py-1.5 rounded-md text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Point of Sale (POS)</a></li>
                    <li><a href="{{ url('/admin/settings/manage-branding') }}" class="block px-2 py-1.5 rounded-md text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Branding & Appearance</a></li>
                    <li><a href="{{ url('/admin/roles') }}" class="block px-2 py-1.5 rounded-md text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Roles & Permissions (RBAC)</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
