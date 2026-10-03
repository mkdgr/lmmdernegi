<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    protected $guarded = [];

    protected $casts = ['consent_at' => 'datetime', 'unsubscribed_at' => 'datetime'];
}
