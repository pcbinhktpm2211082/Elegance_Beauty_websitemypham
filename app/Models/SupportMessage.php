<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'support_id',
        'sender_id',
        'is_admin',
        'message',
        'attachment_path',
        'read_at',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function support()
    {
        return $this->belongsTo(Support::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}


