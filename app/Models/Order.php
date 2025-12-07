<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    /**
     * Giảm số lượng sản phẩm khi đơn hàng được xác nhận
     */
    public function reduceProductQuantities(): void
    {
        try {
            DB::beginTransaction();
            
            $items = $this->orderItems()->with(['product', 'order'])->get();
            
            foreach ($items as $item) {
                if ($item->variant_id) {
                    // Nếu có variant, giảm số lượng variant
                    $variant = \App\Models\ProductVariant::find($item->variant_id);
                    if ($variant) {
                        $oldQuantity = $variant->quantity;
                        $newQuantity = max(0, $oldQuantity - $item->quantity);
                        $variant->update(['quantity' => $newQuantity]);
                        Log::info('Reduced variant quantity', [
                            'variant_id' => $variant->id,
                            'order_item_quantity' => $item->quantity,
                            'old_quantity' => $oldQuantity,
                            'new_quantity' => $newQuantity
                        ]);
                    }
                } else {
                    // Nếu không có variant, giảm số lượng sản phẩm
                    $product = $item->product;
                    if ($product) {
                        $oldQuantity = $product->quantity;
                        $newQuantity = max(0, $oldQuantity - $item->quantity);
                        $product->update(['quantity' => $newQuantity]);
                        Log::info('Reduced product quantity', [
                            'product_id' => $product->id,
                            'order_item_quantity' => $item->quantity,
                            'old_quantity' => $oldQuantity,
                            'new_quantity' => $newQuantity
                        ]);
                    }
                }
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error reducing product quantities', [
                'order_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Khôi phục số lượng sản phẩm khi đơn hàng bị hủy
     */
    public function restoreProductQuantities(): void
    {
        try {
            DB::beginTransaction();
            
            $items = $this->orderItems()->with(['product', 'order'])->get();
            
            foreach ($items as $item) {
                if ($item->variant_id) {
                    // Nếu có variant, khôi phục số lượng variant
                    $variant = \App\Models\ProductVariant::find($item->variant_id);
                    if ($variant) {
                        $oldQuantity = $variant->quantity;
                        $newQuantity = $oldQuantity + $item->quantity;
                        $variant->update(['quantity' => $newQuantity]);
                        Log::info('Restored variant quantity', [
                            'variant_id' => $variant->id,
                            'order_item_quantity' => $item->quantity,
                            'old_quantity' => $oldQuantity,
                            'new_quantity' => $newQuantity
                        ]);
                    }
                } else {
                    // Nếu không có variant, khôi phục số lượng sản phẩm
                    $product = $item->product;
                    if ($product) {
                        $oldQuantity = $product->quantity;
                        $newQuantity = $oldQuantity + $item->quantity;
                        $product->update(['quantity' => $newQuantity]);
                        Log::info('Restored product quantity', [
                            'product_id' => $product->id,
                            'order_item_quantity' => $item->quantity,
                            'old_quantity' => $oldQuantity,
                            'new_quantity' => $newQuantity
                        ]);
                    }
                }
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error restoring product quantities', [
                'order_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Xử lý khi status thay đổi
     */
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($order) {
            $originalStatus = $order->getOriginal('status');
            $newStatus = $order->status;

            // Nếu đơn hàng chuyển từ pending sang processing, giảm số lượng
            if ($originalStatus === 'pending' && $newStatus === 'processing') {
                $order->reduceProductQuantities();
            }
            
            // Nếu đơn hàng chuyển từ processing/shipped/delivered sang cancelled, khôi phục số lượng
            if (in_array($originalStatus, ['processing', 'shipped', 'delivered']) && $newStatus === 'cancelled') {
                $order->restoreProductQuantities();
            }
        });
    }
}
