<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GCashAccount
{
    /** @return array{number: ?string, name: ?string, qr_url: ?string, available: bool} */
    public static function details(): array
    {
        $settings = DB::table('shop_settings')->find(1);
        $number = $settings->gcash_number ?? null;
        $name = $settings->gcash_account_name ?? null;
        $qrPath = $settings->gcash_qr_path ?? null;
        $hasQR = is_string($qrPath) && preg_match('/\Agcash-qr\/[a-zA-Z0-9_-]+\.(?:jpg|jpeg|png|webp)\z/i', $qrPath) && Storage::disk('public')->exists($qrPath);

        return ['number' => $number, 'name' => $name, 'qr_url' => $hasQR ? asset('storage/'.$qrPath) : null,
            'available' => filled($number) && filled($name)];
    }
}
