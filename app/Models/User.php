<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser; // <--- 1. Tambahkan import ini
use Filament\Panel; // <--- 2. Tambahkan import ini
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; 

#[Fillable(['name', 'email', 'password', 'phone', 'role'])] // Pastikan 'role' ada atau sesuai kebutuhan fillable-mu
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser // <--- 3. Tambahkan implements FilamentUser di sini
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable; 

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function savings(): HasMany
    {
        return $this->hasMany(Saving::class);
    }

    // <--- 4. Tambahkan method keamanan ini di dalam class User
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin';
    }
}