<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Định nghĩa các trạng thái đơn hàng
    const STATUS_PENDING    = 'pending';     // Đang chờ xử lý
    const STATUS_PROCESSING = 'processing';  // Đang xử lý
    const STATUS_SHIPPED    = 'shipped';     // Đang giao hàng
    const STATUS_DELIVERED  = 'delivered';   // Đã hoàn thành
    const STATUS_CANCELLED  = 'cancelled';   // Đã hủy

    protected $fillable = [
        'user_id',
        'order_code', 
        'customer_name', 
        'customer_phone', 
        'customer_address',
        'payment_method', 
        'note', 
        'voucher_code',
        'discount_amount',
        'total_price', 
        'status',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'shipped' => 'Đang giao hàng',
            'delivered' => 'Đã hoàn thành',
            'cancelled' => 'Đã hủy',
            default => ucfirst($this->status)
        };
    }

    /**
     * Tên phương thức thanh toán hiển thị tiếng Việt
     */
    public function getPaymentMethodTextAttribute(): string
    {
        return match($this->payment_method) {
            'cash_on_delivery' => 'Thanh toán khi nhận hàng',
            'online_payment' => 'Thanh toán trực tuyến',
            'bank_transfer' => 'Chuyển khoản ngân hàng',
            null, '' => 'Chưa xác định',
            default => $this->payment_method,
        };
    }

    /**
     * Tạm tính = tổng của các dòng hàng
     */
    public function getSubtotalAttribute(): float
    {
        // Sử dụng quan hệ đã nạp để tránh N+1
        $items = $this->relationLoaded('orderItems') ? $this->orderItems : $this->orderItems()->get();
        return (float) $items->sum('total_price');
    }

    /**
     * Phí vận chuyển = tổng đơn - tạm tính (nếu > 0)
     */
    public function getShippingFeeAttribute(): float
    {
        $shipping = (float) $this->total_price - (float) $this->subtotal;
        return $shipping > 0 ? $shipping : 0.0;
    }
}
