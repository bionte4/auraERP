<x-filament-panels::page>
    @php
        $project = $this->record;
        $pmEmployee = $project->user?->employee ?? null;
        $teamLeader = $project->teamLeader;
        $members = $project->employees
            ->reject(fn ($employee) => $teamLeader && $employee->id === $teamLeader->id)
            ->values();
    @endphp

    <div class="space-y-8">
        {{-- Project Manager --}}
        <div class="flex flex-col items-center">
            <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                {{ __('projects::filament/resources/project/pages/manage-org-structure.project-manager') }}
            </p>
            <div class="w-full max-w-sm rounded-xl bg-white p-4 text-center shadow ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <p class="text-base font-semibold text-gray-950 dark:text-white">{{ $project->user?->name ?? '—' }}</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $pmEmployee?->job?->name ?? __('projects::filament/resources/project/pages/manage-org-structure.project-manager') }}
                    @if ($pmEmployee?->department?->name)
                        · {{ $pmEmployee->department->name }}
                    @endif
                </p>
            </div>
            <div class="h-6 w-px bg-gray-300 dark:bg-gray-600"></div>
        </div>

        {{-- Team Leader --}}
        <div class="flex flex-col items-center">
            <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                {{ __('projects::filament/resources/project/pages/manage-org-structure.team-leader') }}
            </p>
            @if ($teamLeader)
                <div class="w-full max-w-sm rounded-xl bg-primary-50 p-4 text-center shadow ring-1 ring-primary-600/20 dark:bg-primary-400/10 dark:ring-primary-400/20">
                    <p class="text-base font-semibold text-gray-950 dark:text-white">{{ $teamLeader->name }}</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ $teamLeader->job?->name ?? '—' }}
                        @if ($teamLeader->department?->name)
                            · {{ $teamLeader->department->name }}
                        @endif
                    </p>
                </div>
                <div class="h-6 w-px bg-gray-300 dark:bg-gray-600"></div>
            @else
                <div class="w-full max-w-sm rounded-xl bg-white p-4 text-center text-sm text-gray-500 shadow ring-1 ring-gray-950/5 dark:bg-gray-900 dark:text-gray-400 dark:ring-white/10">
                    {{ __('projects::filament/resources/project/pages/manage-org-structure.no-team-leader') }}
                </div>
            @endif
        </div>

        {{-- Members --}}
        <div>
            <p class="mb-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                {{ __('projects::filament/resources/project/pages/manage-org-structure.members') }} ({{ $members->count() }})
            </p>
            @if ($members->isEmpty())
                <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                    {{ __('projects::filament/resources/project/pages/manage-org-structure.no-members') }}
                </p>
            @else
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($members as $member)
                        <div class="rounded-xl bg-white p-4 shadow ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                            <p class="font-medium text-gray-950 dark:text-white">{{ $member->name }}</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ $member->job?->name ?? '—' }}
                                @if ($member->department?->name)
                                    · {{ $member->department->name }}
                                @endif
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
