<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Support extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email', 
        'title',
        'message',
        'status', 
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function messages()
    {
        return $this->hasMany(SupportMessage::class);
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'completed' => 'Đã hoàn thành',
            'cancelled' => 'Đã hủy'
        ];

        return $statuses[$this->status] ?? 'Không xác định';
    }

    public function getStatusClassAttribute()
    {
        $classes = [
            'pending' => 'status-pending',
            'processing' => 'status-processing',
            'completed' => 'status-completed',
            'cancelled' => 'status-cancelled'
        ];

        return $classes[$this->status] ?? 'status-unknown';
    }
}

