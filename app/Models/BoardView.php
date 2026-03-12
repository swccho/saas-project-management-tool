<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoardView extends Model
{
    use HasFactory;

    protected $fillable = [
        'board_id',
        'user_id',
        'name',
        'filter_config',
        'sort_config',
        'is_pinned',
    ];

    protected function casts(): array
    {
        return [
            'filter_config' => 'array',
            'sort_config' => 'array',
            'is_pinned' => 'boolean',
        ];
    }

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
