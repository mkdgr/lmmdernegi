<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    public const RELATIONS = ['hasta' => 'Hastayım', 'yakin' => 'Hasta yakınıyım', 'diger' => 'Diğer'];

    public const STATUSES = ['yeni' => 'Yeni', 'yanitlandi' => 'Yanıtlandı', 'arsiv' => 'Arşiv'];

    protected $guarded = [];

    protected $casts = ['answered_at' => 'datetime', 'consent_at' => 'datetime'];

    public function disease(): BelongsTo
    {
        return $this->belongsTo(Disease::class);
    }

    public function answerer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'answered_by');
    }
}
