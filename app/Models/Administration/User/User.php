<?php

namespace App\Models\Administration\User;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Configuration\Occupation\Occupation;
use App\Models\Organization\OrganizationChart\OrganizationChart;
use App\Models\Traits\HasUuid;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, HasUuid, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'name_filter',
        'email',
        'password',
        'password_default',
        'avatar',
        'phone_personal',
        'phone_work',
        'matriculation',
        'cpf',
        'birth_date',
        'is_active',
        'gender_id',
        'occupation_id',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
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
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function occupation(): BelongsTo
    {
        return $this->belongsTo(Occupation::class, 'occupation_id');
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(OrganizationChart::class, 'organization_chart_user', 'user_id', 'organization_chart_id')
            ->withTimestamps();
    }

    // Criação do Filter Name
    public function setNameAttribute(string $value): void
    {
        $this->attributes['name'] = $value;
        $this->attributes['name_filter'] = Str::ascii(strtolower($value));
    }
}
