<?php

namespace Webkul\Security\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Webkul\Security\Models\User;
use Webkul\Security\Services\DatabaseBackupManager;

class DatabaseBackupDownloadController
{
    public function __invoke(Request $request, string $filename, DatabaseBackupManager $backups): BinaryFileResponse
    {
        $user = $request->user();
        $adminRole = config('filament-shield.panel_user.name', 'Admin');

        abort_unless(
            $user instanceof User && method_exists($user, 'hasRole') && $user->hasRole($adminRole),
            403
        );

        $path = $backups->absolutePath($filename);

        return response()->download($path, basename($path));
    }
}
