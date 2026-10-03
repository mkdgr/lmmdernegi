<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipApplication extends Model
{
    public const RELATIONS = ['hasta' => 'Hasta', 'yakin' => 'Hasta yakını', 'hekim' => 'Sağlık çalışanı', 'gonullu' => 'Destekçi / gönüllü'];

    public const STATUSES = ['yeni' => 'Yeni', 'onaylandi' => 'Onaylandı', 'reddedildi' => 'Reddedildi'];

    protected $guarded = [];

    protected $casts = [
        'tckn' => 'encrypted',
        'birth_date' => 'date',
        'consent_at' => 'datetime',
    ];
}
