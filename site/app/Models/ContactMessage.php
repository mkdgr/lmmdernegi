<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    public const STATUSES = ['yeni' => 'Yeni', 'okundu' => 'Okundu', 'arsiv' => 'Arşiv'];

    protected $guarded = [];

    protected $casts = ['consent_at' => 'datetime'];
}
