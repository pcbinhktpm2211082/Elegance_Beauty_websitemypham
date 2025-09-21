<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Cart extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'product_id',
        'variant_id',
        'quantity',
        'price'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    // Accessors
    public function getSubtotalAttribute()
    {
        return $this->price * $this->quantity;
    }

    public function getProductNameAttribute()
    {
        $name = $this->product->name;
        if ($this->variant) {
            $name .= ' - ' . $this->variant->variant_name;
        }
        return $name;
    }

    public function getProductImageAttribute()
    {
        if ($this->variant && $this->variant->image) {
            return $this->variant->image;
        }
        return $this->product->coverOrFirstImage;
    }

    // Static methods
    public static function getCartItems($userId = null, $sessionId = null)
    {
        try {
            $query = self::with(['product', 'variant']);
            
            if ($userId) {
                $query->where('user_id', $userId);
            } elseif ($sessionId) {
                $query->where('session_id', $sessionId);
            }
            
            return $query->get();
        } catch (\Exception $e) {
            Log::error('Cart getCartItems error: ' . $e->getMessage());
            return collect();
        }
    }

    public static function getCartCount($userId = null, $sessionId = null)
    {
        try {
            $query = self::query();
            
            if ($userId) {
                $query->where('user_id', $userId);
            } elseif ($sessionId) {
                $query->where('session_id', $sessionId);
            }
            
            return $query->sum('quantity');
        } catch (\Exception $e) {
            Log::error('Cart getCartCount error: ' . $e->getMessage());
            return 0;
        }
    }

    public static function getCartTotal($userId = null, $sessionId = null)
    {
        try {
            $items = self::getCartItems($userId, $sessionId);
            return $items->sum('subtotal');
        } catch (\Exception $e) {
            Log::error('Cart getCartTotal error: ' . $e->getMessage());
            return 0;
        }
    }

    public static function clearCart($userId = null, $sessionId = null)
    {
        try {
            $query = self::query();
            
            if ($userId) {
                $query->where('user_id', $userId);
            } elseif ($sessionId) {
                $query->where('session_id', $sessionId);
            }
            
            return $query->delete();
        } catch (\Exception $e) {
            Log::error('Cart clearCart error: ' . $e->getMessage());
            return false;
        }
    }
}
