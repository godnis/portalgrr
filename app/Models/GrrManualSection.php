<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GrrManualSection extends Model
{
    protected $table = 'grr_manual_sections';

    protected $fillable = [
        'manual_id',
        'code',
        'anchor',
        'title',
        'subtitle',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function manual(): BelongsTo
    {
        return $this->belongsTo(GrrManual::class, 'manual_id');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(GrrManualArticle::class, 'section_id')
            ->orderBy('sort_order');
    }
}