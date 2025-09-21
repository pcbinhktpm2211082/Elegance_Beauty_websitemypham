<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::orderByDesc('created_at')->paginate(12);
        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        return view('admin.vouchers.create');
    }

    public function store(Request $request)
    {
        // Chuẩn hoá checkbox trước khi validate (trả về true/false)
        $request->merge(['is_active' => $request->boolean('is_active')]);

        $data = $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code',
            'description' => 'nullable|string|max:255',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'usage_limit' => 'nullable|integer|min:1',
            'is_active' => 'sometimes|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        // Chuẩn hoá datetime từ input type "datetime-local"
        if (!empty($data['start_date'])) {
            $data['start_date'] = Carbon::parse($data['start_date']);
        }
        if (!empty($data['end_date'])) {
            $data['end_date'] = Carbon::parse($data['end_date']);
        }

        Voucher::create($data);
        return redirect()->route('admin.vouchers.index')->with('success', 'Tạo voucher thành công');
    }

    public function edit(Voucher $voucher)
    {
        return view('admin.vouchers.edit', compact('voucher'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        // Chuẩn hoá checkbox trước khi validate (trả về true/false)
        $request->merge(['is_active' => $request->boolean('is_active')]);

        $data = $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code,' . $voucher->id,
            'description' => 'nullable|string|max:255',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'usage_limit' => 'nullable|integer|min:1',
            'is_active' => 'sometimes|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        if (!empty($data['start_date'])) {
            $data['start_date'] = Carbon::parse($data['start_date']);
        }
        if (!empty($data['end_date'])) {
            $data['end_date'] = Carbon::parse($data['end_date']);
        }

        $voucher->update($data);
        return redirect()->route('admin.vouchers.index')->with('success', 'Cập nhật voucher thành công');
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return redirect()->route('admin.vouchers.index')->with('success', 'Đã xoá voucher');
    }
}


