<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    protected $guarded = [];

    public static function normalize(string $path): string
    {
        $path = '/'.ltrim(rawurldecode(parse_url($path, PHP_URL_PATH) ?? $path), '/');

        return mb_strtolower($path);
    }

    protected static function booted(): void
    {
        static::saving(fn ($r) => $r->from_path = static::normalize($r->from_path));
    }
}
