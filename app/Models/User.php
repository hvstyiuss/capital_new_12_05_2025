<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Parcours;
use App\Models\Entite;
use App\Models\AppNotification;
use App\Models\Deplacement;
use App\Models\HorsBareme;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $primaryKey = 'ppr';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'ppr',
        'fname',
        'lname',
        'email',
        'email_verified_at',
        'password',
        'is_active',
        'is_deleted',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_deleted' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        // Global scope to exclude deleted users
        static::addGlobalScope('not_deleted', function (Builder $builder) {
            $builder->where('is_deleted', false);
        });
    }

    /**
     * Get the user info for this user.
     */
    public function userInfo(): HasOne
    {
        return $this->hasOne(UserInfo::class, 'ppr', 'ppr');
    }

    /**
     * Get the user settings for this user.
     */
    public function userSetting(): HasOne
    {
        return $this->hasOne(UserSetting::class, 'ppr', 'ppr');
    }

    /**
     * Get the career path entries for this user.
     */
    public function parcours(): HasMany
    {
        return $this->hasMany(Parcours::class, 'ppr', 'ppr');
    }

    /**
     * Get the annual notes for this user.
     */
    public function noteAnnuelles(): HasMany
    {
        return $this->hasMany(NoteAnnuelle::class, 'ppr', 'ppr');
    }

    /**
     * Get the requests made by this user.
     */
    public function demandes(): HasMany
    {
        return $this->hasMany(Demande::class, 'ppr', 'ppr');
    }

    /**
     * Get the announcements made by this user.
     */
    public function annonces(): HasMany
    {
        return $this->hasMany(Annonce::class, 'ppr', 'ppr');
    }

    /**
     * Get the suggestions made by this user.
     */
    public function suggestions(): HasMany
    {
        return $this->hasMany(Suggestion::class, 'ppr', 'ppr');
    }

    /**
     * Get the deplacements for this user.
     */
    public function deplacements(): HasMany
    {
        return $this->hasMany(Deplacement::class, 'ppr', 'ppr');
    }

    /**
     * Get the hors bareme entries for this user.
     */
    public function horsBaremes(): HasMany
    {
        return $this->hasMany(HorsBareme::class, 'ppr', 'ppr');
    }

    /**
     * Get the organizational entities this user belongs to.
     */
    public function entites(): BelongsToMany
    {
        return $this->belongsToMany(Entite::class, 'parcours', 'ppr', 'entite_id')
            ->withPivot('poste', 'date_debut', 'date_fin', 'grade_id', 'reason')
            ->withTimestamps();
    }

    /**
     * Get the full name attribute (combines fname and lname).
     */
    public function getNameAttribute(): string
    {
        $fname = $this->attributes['fname'] ?? '';
        $lname = $this->attributes['lname'] ?? '';
        return trim($fname . ' ' . $lname);
    }

    /**
     * Check if user is a chef (is chef_ppr in any entite)
     */
    public function isChef(): bool
    {
        return Entite::where('chef_ppr', $this->ppr)->exists();
    }

    /**
     * Check if user is a director of a direction (chef of a direction entity)
     */
    public function isDirectorOfDirection(): bool
    {
        $mutationService = app(\App\Services\MutationService::class);
        $chefEntites = Entite::where('chef_ppr', $this->ppr)->get();

        foreach ($chefEntites as $entite) {
            if ($mutationService->isDirection($entite)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the image attribute (returns photo from userInfo if available).
     */
    public function getImageAttribute(): ?string
    {
        // Ensure userInfo relationship is loaded
        if (!$this->relationLoaded('userInfo')) {
            $this->load('userInfo');
        }
        return $this->userInfo?->photo;
    }

    /**
     * Get the notifications for this user.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(AppNotification::class, 'ppr', 'ppr');
    }

    /**
     * Get the email notifications attribute (returns from userSetting).
     */
    public function getEmailNotificationsAttribute(): bool
    {
        // Ensure userSetting relationship is loaded
        if (!$this->relationLoaded('userSetting')) {
            $this->load('userSetting');
        }
        return $this->userSetting?->notifications_email ?? true;
    }

    /**
     * Get the push notifications attribute (returns from userSetting).
     */
    public function getPushNotificationsAttribute(): bool
    {
        // Ensure userSetting relationship is loaded
        if (!$this->relationLoaded('userSetting')) {
            $this->load('userSetting');
        }
        return $this->userSetting?->notifications_sms ?? false;
    }

    // Note: The HasRoles trait from Spatie Permission provides the roles() relationship
    // If you need a custom roles relationship, you should rename it to avoid conflicts
}
