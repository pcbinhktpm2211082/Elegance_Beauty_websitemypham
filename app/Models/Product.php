<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
        protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
        'quantity',
        'image',         
        'is_active',
        'is_featured',     
    ];



    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }


    public function coverImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_cover', true);
    }

    public function firstImage()
    {
        return $this->hasOne(ProductImage::class)->orderBy('sort_order');
    }

    public function getCoverOrFirstImageAttribute()
    {
        // Lấy ảnh có is_cover = true trước, nếu không có thì lấy ảnh đầu tiên
        $image = $this->images->firstWhere('is_cover', true) ?? $this->images->first();
        return $image ? $image->image_path : null;
    }



    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
    
    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'product_attribute')
                    ->with('values');
    }
    
    public function getOriginalPriceAttribute()
    {
        // Có thể implement logic tính giá gốc dựa trên discount hoặc giá cao nhất của variants
        return $this->price;
    }
    
    public function getHasVariantsAttribute()
    {
        return $this->variants()->exists();
    }
    
    public function getTotalQuantityAttribute()
    {
        if ($this->has_variants) {
            return $this->variants->sum('quantity');
        }
        return $this->quantity;
    }


}
