<?php

namespace Webkul\Security\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Webkul\Security\Models\User;
use Webkul\Security\Services\DatabaseBackupManager;
use Webkul\Security\Support\AdminAccess;

class DatabaseBackupDownloadController
{
    public function __invoke(Request $request, string $filename, DatabaseBackupManager $backups): BinaryFileResponse
    {
        $user = $request->user();

        abort_unless($user instanceof User && AdminAccess::check($user), 403);

        $path = $backups->absolutePath($filename);

        return response()->download($path, basename($path));
    }
}
