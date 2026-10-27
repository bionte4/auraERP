<x-filament-panels::page>
    @php
        $project = $this->record;
        $pmEmployee = $project->user?->employee ?? null;
        $director = $project->director;
        $teamLeader = $project->teamLeader;
        $unitRoleIds = $project->units
            ->flatMap(fn ($unit) => [$unit->mos_employee_id, $unit->team_leader_employee_id])
            ->filter()
            ->all();
        $members = $project->employees
            ->reject(fn ($employee) => ($teamLeader && $employee->id === $teamLeader->id) || in_array($employee->id, $unitRoleIds, true))
            ->values();
    @endphp

    <div class="space-y-8">
        @if ($project->is_strategic)
            <div class="flex flex-col items-center">
                <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    {{ __('projects::filament/resources/project/pages/manage-org-structure.project-director') }}
                </p>
                <div class="w-full max-w-sm rounded-xl bg-amber-50 p-4 text-center shadow ring-1 ring-amber-600/20 dark:bg-amber-400/10 dark:ring-amber-400/20">
                    <p class="text-base font-semibold text-gray-950 dark:text-white">{{ $director?->name ?? '—' }}</p>
                    @unless ($director)
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('projects::filament/resources/project/pages/manage-org-structure.no-director') }}
                        </p>
                    @endunless
                </div>
                <div class="h-6 w-px bg-gray-300 dark:bg-gray-600"></div>
            </div>
        @endif

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

        @if ($project->is_strategic)
            <div>
                <p class="mb-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    {{ __('projects::filament/resources/project/pages/manage-org-structure.units') }} ({{ $project->units->count() }})
                </p>
                @forelse ($project->units as $unit)
                    <div class="mb-4 rounded-xl bg-white p-4 shadow ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                        <p class="mb-3 text-center text-sm font-semibold text-gray-950 dark:text-white">
                            {{ $unit->department?->name ?? '—' }}
                        </p>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="rounded-lg bg-sky-50 p-3 text-center dark:bg-sky-400/10">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ __('projects::filament/resources/project/pages/manage-org-structure.mos') }}
                                </p>
                                <p class="mt-1 font-medium text-gray-950 dark:text-white">{{ $unit->mos?->name ?? '—' }}</p>
                            </div>
                            <div class="rounded-lg bg-primary-50 p-3 text-center dark:bg-primary-400/10">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ __('projects::filament/resources/project/pages/manage-org-structure.unit-team-leader') }}
                                </p>
                                <p class="mt-1 font-medium text-gray-950 dark:text-white">{{ $unit->teamLeader?->name ?? '—' }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                        {{ __('projects::filament/resources/project/pages/manage-org-structure.no-units') }}
                    </p>
                @endforelse
            </div>
        @else
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
        @endif

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
