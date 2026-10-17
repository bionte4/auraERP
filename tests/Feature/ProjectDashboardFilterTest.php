<?php

use Webkul\Project\Filament\Pages\Dashboard;

it('normalizes scalar and array dashboard filter values for whereIn queries', function (): void {
    expect(Dashboard::selectedFilterIds(null))->toBe([])
        ->and(Dashboard::selectedFilterIds(''))->toBe([])
        ->and(Dashboard::selectedFilterIds(12))->toBe([12])
        ->and(Dashboard::selectedFilterIds(['7', '', null, 9]))->toBe(['7', 9]);
});
