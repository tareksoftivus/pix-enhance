<?php

namespace App\Models;

use App\Modules\AuthApi\Models\SocialAccount;
use App\Modules\Billing\Models\BillingInvoice;
use App\Modules\Credits\Models\CreditReservation;
use App\Modules\Credits\Models\CreditTransaction;
use App\Modules\Credits\Models\CreditWallet;
use App\Modules\NotificationTemplates\Traits\HasDeviceTokens;
use App\Modules\RenderJobs\Models\RenderJob;
use App\Modules\UserWorkspace\Models\UserWorkspacePreference;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use NotificationChannels\WebPush\HasPushSubscriptions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasDeviceTokens, HasFactory, HasPushSubscriptions, HasRoles, Notifiable, SoftDeletes;

    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'last_login_at',
        'last_login_ip',
        'avatar',
        'phone',
        'phone_verified_at',
        'phone_verification_code',
        'otp_two_factor_enabled',
        'otp_two_factor_channel',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'otp_two_factor_enabled' => 'boolean',
        ];
    }

    public function hasVerifiedPhone(): bool
    {
        return ! is_null($this->phone_verified_at);
    }

    public function hasTwoFactorEnabled(): bool
    {
        return (bool) $this->two_factor_enabled;
    }

    public function hasOtpTwoFactorEnabled(): bool
    {
        return (bool) $this->otp_two_factor_enabled;
    }

    public function hasEmailTwoFactorEnabled(): bool
    {
        return (bool) $this->email_two_factor_enabled;
    }

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function workspacePreference(): HasOne
    {
        return $this->hasOne(UserWorkspacePreference::class);
    }

    public function creditWallet(): HasOne
    {
        return $this->hasOne(CreditWallet::class);
    }

    public function creditTransactions(): HasMany
    {
        return $this->hasMany(CreditTransaction::class);
    }

    public function creditReservations(): HasMany
    {
        return $this->hasMany(CreditReservation::class);
    }

    public function renderJobs(): HasMany
    {
        return $this->hasMany(RenderJob::class);
    }

    public function billingInvoices(): HasMany
    {
        return $this->hasMany(BillingInvoice::class, 'billable_id')
            ->where('billable_type', $this->getMorphClass());
    }
}
