<?php

namespace App\Models;

use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use Billable, HasFactory, Notifiable;

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
            'role' => Role::class,
            'trial_ends_at' => 'datetime',
        ];
    }

    public function isAuthor(): bool
    {
        return $this->role === Role::Author || $this->role === Role::Admin;
    }

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    /**
     * Determine if the user has an active subscription.
     *
     * Cashier's `subscribed` returns true during the grace period of a canceled
     * subscription (until `ends_at`), so users keep ad-free access until the end
     * of their current billing cycle rather than losing it mid-cycle (PRD §6).
     */
    public function hasActiveSubscription(): bool
    {
        return $this->subscribed('default');
    }

    /**
     * Determine if the user should be shown advertisements.
     *
     * Only active subscribers get an ad-free experience; everyone else,
     * including guests, sees ads.
     */
    public function canViewAds(): bool
    {
        return ! $this->hasActiveSubscription();
    }
}
