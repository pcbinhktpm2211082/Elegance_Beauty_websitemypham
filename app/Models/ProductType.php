<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'requires_skin_type_filter',
    ];

    protected $casts = [
        'requires_skin_type_filter' => 'boolean',
    ];

    /**
     * Lấy tất cả product types được sắp xếp theo tên
     */
    public static function getOrdered()
    {
        return static::orderBy('name')->get();
    }
}
