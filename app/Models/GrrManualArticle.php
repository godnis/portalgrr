<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GrrManualArticle extends Model
{
    protected $table = 'grr_manual_articles';

    protected $fillable = [
        'section_id',
        'article_number',
        'title',
        'body',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(GrrManualSection::class, 'section_id');
    }
}