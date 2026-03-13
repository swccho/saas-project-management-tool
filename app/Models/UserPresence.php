<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPresence extends Model
{
    public const STATUS_ONLINE = 'online';

    public const STATUS_AWAY = 'away';

    public const STATUS_OFFLINE = 'offline';

    protected $table = 'user_presence';

    protected $fillable = [
        'user_id',
        'status',
        'last_seen_at',
    ];

    protected function casts(): array
    {
        return [
            'last_seen_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
