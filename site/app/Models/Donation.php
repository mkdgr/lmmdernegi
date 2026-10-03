<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Donation extends Model
{
    public const TYPES = ['bagis' => 'Bağış', 'aidat' => 'Üyelik aidatı', 'giris' => 'Üyelik giriş aidatı'];

    public const FREQUENCIES = ['tek' => 'Tek seferlik', 'aylik' => 'Her ay düzenli'];

    public const STATUSES = ['beklemede' => 'Ödeme bekleniyor', 'basarili' => 'Başarılı', 'basarisiz' => 'Başarısız'];

    protected $guarded = [];

    protected $casts = [
        'tckn' => 'encrypted',
        'bank_response' => 'array',
        'is_anonymous' => 'boolean',
        'paid_at' => 'datetime',
        'consent_at' => 'datetime',
    ];

    public static function newOrderId(): string
    {
        // Garanti: en fazla 36 karakter, harf/rakam
        return 'LLM'.now()->format('ymdHis').Str::upper(Str::random(6));
    }

    public function isPaid(): bool
    {
        return $this->status === 'basarili';
    }
}
