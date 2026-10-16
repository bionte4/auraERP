<?php

use Illuminate\Support\Facades\Route;
use Webkul\Security\Http\Controllers\DatabaseBackupDownloadController;
use Webkul\Security\Livewire\AcceptInvitation;

Route::middleware(['web'])->group(function () {
    Route::middleware('signed')
        ->get('invitation/{invitation}/accept', AcceptInvitation::class)
        ->name('security.invitation.accept');

    Route::middleware(['auth'])
        ->get('admin/database-backups/{filename}/download', DatabaseBackupDownloadController::class)
        ->where('filename', '[A-Za-z0-9._-]+')
        ->name('security.database-backups.download');
});
