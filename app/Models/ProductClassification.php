<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductClassification extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_product_classification');
    }
}
