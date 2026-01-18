<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

use Filament\Models\Contracts\HasTenants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class User extends Authenticatable implements FilamentUser, HasMedia
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, InteractsWithMedia, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    const ACTIVE = 1;

    const INACTIVE = 0;

    const PROFILE = 'profile';

    protected $fillable = [
        'name',
        'email',
        'restaurant_id',
        'parent_user_id',
        'status',
        'password',
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
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }



    public function settings()
    {
        return $this->hasMany(Setting::class, 'user_id', 'id');
    }

    public static function getFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Name:')
                ->placeholder('Name')
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->placeholder('Email')
                ->label('Email:')
                ->email()
                ->unique('users', 'email', ignoreRecord: true)
                ->required()
                ->maxLength(255),
            TextInput::make('password')
                ->placeholder('Password')
                ->label('Password  :')
                ->password()
                ->visibleOn('create')
                ->revealable()
                ->required()
                ->maxLength(255)
                ->dehydrateStateUsing(fn ($state) => bcrypt($state)),
            TextInput::make('password_confirmation')
                ->label('Confirm Password:')
                ->placeholder('Confirm Password')
                ->password()
                ->same('password')
                ->revealable()
                ->visibleOn('create')
                ->required()
                ->maxLength(255)
                ->dehydrateStateUsing(fn ($state) => bcrypt($state)),
            Toggle::make('status')
                ->label('Status')
                ->default(self::ACTIVE)
                ->required(),
        ];
    }
}
