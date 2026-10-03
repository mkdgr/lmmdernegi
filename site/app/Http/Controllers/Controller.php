<?php

namespace App\Http\Controllers;

use App\Mail\AdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

abstract class Controller
{
    /** Gizli "website" alanı doluysa gönderen bir bottur. */
    protected function isSpam(Request $request): bool
    {
        return filled($request->input('website'));
    }

    /** Derneğe e-posta bildirimi; e-posta ayarı bozuk olsa bile ziyaretçiye hata gösterilmez. */
    protected function notifyAdmin(string $title, array $fields, ?string $panelUrl = null, ?string $replyTo = null): void
    {
        try {
            Mail::to(setting('notify_email', config('site.notify_email')))
                ->send(new AdminNotification($title, $fields, $panelUrl, $replyTo));
        } catch (\Throwable $e) {
            Log::warning('Yönetici bildirimi gönderilemedi: '.$e->getMessage());
        }
    }
}
