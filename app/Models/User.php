<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'gender',
        'dob',
        'avatar',
        'address',
        'city',
        'district',
        'ward',
        'role',
        'status',
        'provider',
        'provider_id',
        'email_verified_at',
        'skin_type',
        'skin_concerns',
        'is_sensitive',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'dob' => 'date',
        'status' => 'boolean',
        'skin_concerns' => 'array',
        'is_sensitive' => 'boolean',
    ];

    // Accessor methods
    public function getGenderTextAttribute()
    {
        return match($this->gender) {
            'male' => 'Nam',
            'female' => 'Nữ',
            'other' => 'Khác',
            default => 'Chưa cập nhật',
        };
    }

    public function getSkinTypeTextAttribute()
    {
        return match($this->skin_type) {
            'normal' => 'Da Thường',
            'dry' => 'Da Khô',
            'oily' => 'Da Dầu/Nhờn',
            'combination' => 'Da Hỗn Hợp',
            'sensitive' => 'Da Nhạy Cảm',
            default => 'Chưa xác định',
        };
    }

    public function getFullAddressAttribute()
    {
        $parts = array_filter([$this->address, $this->ward, $this->district, $this->city]);
        return implode(', ', $parts);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function productViews()
    {
        return $this->hasMany(ProductView::class);
    }
}
