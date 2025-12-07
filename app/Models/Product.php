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
        'product_type',
        'sensitive_flags',
    ];

    protected $casts = [
        'sensitive_flags' => 'array',
    ];



    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
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

    public function classifications()
    {
        return $this->belongsToMany(ProductClassification::class, 'product_product_classification');
    }

    public function skinTypes()
    {
        return $this->classifications()->where('type', 'skin_type');
    }

    public function skinConcerns()
    {
        return $this->classifications()->where('type', 'skin_concern');
    }

    public function views()
    {
        return $this->hasMany(ProductView::class);
    }

    /**
     * Lấy đánh giá đã được duyệt
     */
    public function approvedReviews()
    {
        return $this->reviews()->where('is_approved', true);
    }

    /**
     * Tính điểm đánh giá trung bình
     */
    public function getAverageRatingAttribute(): float
    {
        $avg = $this->approvedReviews()->avg('rating');
        return $avg ? round($avg, 1) : 0;
    }

    /**
     * Lấy số lượng đánh giá đã được duyệt
     */
    public function getReviewsCountAttribute(): int
    {
        return $this->approvedReviews()->count();
    }

    /**
     * Lấy các order items của sản phẩm
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Tính số lượt bán (tổng quantity từ order_items của các order đã xác nhận)
     */
    public function getSalesCountAttribute(): int
    {
        return $this->orderItems()
            ->whereHas('order', function($q) {
                $q->whereIn('status', ['processing', 'shipped', 'delivered']);
            })
            ->sum('quantity');
    }

}
