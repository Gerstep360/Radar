<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'push_2fa_expires_at' => 'datetime',
            'push_reset_expires_at' => 'datetime',
        ];
    }

    /**
     * Get the user's FCM tokens
     */
    public function fcmTokens()
    {
        return $this->hasMany(FcmToken::class);
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Check if the user is an Administrator (A)
     */
    public function isAdmin(): bool
    {
        if (method_exists($this, 'hasRole') && ($this->hasRole('admin') || $this->hasRole('Administrador') || $this->hasRole('A'))) {
            return true;
        }
        return in_array(strtolower($this->role), ['a', 'admin', 'administrador']);
    }

    /**
     * Check if the user is a Moderator (M)
     */
    public function isModerator(): bool
    {
        if (method_exists($this, 'hasRole') && ($this->hasRole('moderador') || $this->hasRole('moderator') || $this->hasRole('M'))) {
            return true;
        }
        return in_array(strtolower($this->role), ['m', 'moderator', 'moderador']);
    }

    /**
     * Check if the user is a Normal User (U)
     */
    public function isUser(): bool
    {
        if (method_exists($this, 'hasRole') && ($this->hasRole('ciudadano') || $this->hasRole('user') || $this->hasRole('U'))) {
            return true;
        }
        return in_array(strtolower($this->role), ['u', 'user', 'usuario', 'ciudadano']);
    }


    // Un usuario hace muchos reportes
    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    // Un admin (usuario con rol) aparece en logs
    public function adminLogs()
    {
        return $this->hasMany(ReportLog::class, 'admin_id');
    }
}
