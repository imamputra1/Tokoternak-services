<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'phone',
        'farm_name',
        'gender',
        'birth_date',
        'photo',
        'address',
        'city',
        'otp_register',
        'email_verified_at',
        'password',
        'social_media_provider',
        'social_media_id',
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

    // public function getApiResponseAttribute()
    // {
    //     return [
    //         'name' => $this->name,
    //         'email' => $this->email,
    //         'photo' => $this->photo_url,
    //         'username' => $this->username,
    //         'phone' => $this->phone,
    //         'farm_name' => $this->farm_name,
    //         'gender' => $this->gender,
    //         'birth_date' => $this->birth_date,
    //         'address' => $this->address,
    //         'city' => $this->city,

    //     ];
    // }
    public function getApiResponseAttribute()
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'photo_url' => $this->photo_url,
            'username' => $this->username,
            'phone' => $this->phone,
            'farm_name' => $this->farm_name,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date,
            'address' => $this->address,
            'city' => $this->city,
        ];
    }
    public function getPhotoUrlAttribute()
    {
        if (is_null($this->photo)) {
            return null;
        }

        return asset('storage/' . $this->photo);
    }

}
