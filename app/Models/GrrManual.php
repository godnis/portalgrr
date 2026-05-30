<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GrrManual extends Model
{
    protected $table = 'grr_manuals';

    protected $fillable = [
        'title',
        'slug',
        'kicker',
        'subtitle',
        'description',
        'status_label',
        'environment_label',
        'alert_title',
        'alert_text',
        'is_published',
        'version',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'version' => 'integer',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(GrrManualSection::class, 'manual_id')
            ->orderBy('sort_order');
    }
}