<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSetting extends Model
{
    protected $fillable = [
        'ppr',
        'language',
        'theme',
        'timezone',
        'notifications_email',
        'notifications_sms',
        'dark_mode',
        'two_factor_enabled',
    ];

    protected $casts = [
        'notifications_email' => 'boolean',
        'notifications_sms' => 'boolean',
        'dark_mode' => 'boolean',
        'two_factor_enabled' => 'boolean',
    ];

    /**
     * Get the user that owns these settings.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ppr', 'ppr');
    }
}
