<?php

namespace Webkul\Integrations\Support;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class SecretVault
{
    public static function encrypt(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        return Crypt::encryptString($value);
    }

    public static function decrypt(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (DecryptException) {
            return $value;
        }
    }

    public static function keepOrEncrypt(?string $incoming, ?string $existingEncrypted): ?string
    {
        if ($incoming === null || trim($incoming) === '') {
            return $existingEncrypted;
        }

        return self::encrypt($incoming);
    }
}
