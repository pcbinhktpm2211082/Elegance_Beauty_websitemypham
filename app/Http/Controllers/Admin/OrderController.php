<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // Danh sách đơn hàng có lọc trạng thái, tìm kiếm
    public function index(Request $request)
    {
        $query = Order::query();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_code', 'like', "%$search%")
                ->orWhere('customer_name', 'like', "%$search%");
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.orders.index', compact('orders'));
    }


    // Xem chi tiết đơn hàng
    public function show($id)
    {
        $order = Order::with('items')->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    // Cập nhật trạng thái đơn hàng
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'note' => 'nullable|string',
        ]);
        $order->status = $request->status;
        if ($request->note) {
            $order->note = $request->note;
        }
        $order->save();

        // Lấy query parameters từ referer để giữ lại filter ban đầu
        // KHÔNG lấy 'status' từ form (đó là trạng thái mới của đơn hàng, không phải filter)
        $queryParams = [];
        
        // Lấy 'search' từ request nếu có
        if ($request->has('search') && $request->filled('search')) {
            $queryParams['search'] = $request->search;
        }
        
        // Lấy 'filter_status' từ request (nếu có) - đây là filter ban đầu, không phải status mới
        if ($request->has('filter_status') && $request->filled('filter_status')) {
            $queryParams['status'] = $request->filter_status;
        } else {
            // Nếu không có filter_status trong request, lấy từ referer
            $referer = $request->headers->get('referer');
            if ($referer) {
                $parsedUrl = parse_url($referer);
                if (isset($parsedUrl['query'])) {
                    parse_str($parsedUrl['query'], $refererParams);
                    // Lấy status filter từ referer (nếu có)
                    if (isset($refererParams['status'])) {
                        $queryParams['status'] = $refererParams['status'];
                    }
                    // Nếu có search trong referer và chưa có trong queryParams, lấy từ referer
                    if (isset($refererParams['search']) && !isset($queryParams['search'])) {
                        $queryParams['search'] = $refererParams['search'];
                    }
                }
            }
        }
        
        // Nếu có query parameters, redirect về index với query params
        if (!empty($queryParams)) {
            return redirect()->route('admin.orders.index', $queryParams)->with('success', 'Cập nhật trạng thái thành công');
        }

        // Mặc định redirect về index (không có filter)
        return redirect()->route('admin.orders.index')->with('success', 'Cập nhật trạng thái thành công');
    }

    // Xoá đơn hàng
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Xoá đơn hàng thành công');
    }

    
}

