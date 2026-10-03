<?php

namespace App\Models;

use App\Models\Concerns\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Publication extends Model
{
    use Translatable;

    public const KINDS = ['brosur' => 'Broşür', 'bulten' => 'LLMBİR Bülten', 'rapor' => 'Rapor'];

    public array $translatable = ['title', 'description'];

    protected $guarded = [];

    protected $casts = ['published_on' => 'date', 'is_published' => 'boolean'];

    public function url(): ?string
    {
        if ($this->file) {
            return Storage::disk('public')->url($this->file);
        }

        return $this->external_url;
    }
}
