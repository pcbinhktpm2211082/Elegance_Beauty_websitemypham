<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Danh sách người dùng + tìm kiếm + phân trang (chỉ hiển thị khách hàng)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $users = User::query()
            ->where('role', 'user') // Chỉ hiển thị tài khoản khách hàng
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($status !== null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        return view('admin.users.index', compact('users'));
    }

    /**
     * Form thêm mới
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Lưu người dùng mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role'     => 'required|in:user,admin',
            'status'   => 'required|boolean',
            'avatar'   => 'nullable|image|max:2048',
        ]);

        $data = $request->except(['password', 'avatar']);
        $data['password'] = Hash::make($request->password);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'Đã thêm người dùng!');
    }

    /**
     * Xem chi tiết người dùng
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Form chỉnh sửa
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Cập nhật người dùng
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
            'role'     => 'required|in:user,admin',
            'status'   => 'required|boolean',
            'avatar'   => 'nullable|image|max:2048',
        ]);

        $data = $request->except(['password', 'avatar']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật thành công!');
    }

    /**
     * Khóa/mở khóa tài khoản
     */
    public function toggleStatus(User $user)
    {
        // Chỉ cho phép khóa/mở khóa tài khoản khách hàng
        if ($user->role !== 'user') {
            return redirect()->route('admin.users.index')->with('error', 'Không thể thao tác với tài khoản admin!');
        }

        $user->update([
            'status' => !$user->status
        ]);

        $action = $user->status ? 'mở khóa' : 'khóa';
        return redirect()->route('admin.users.index')->with('success', "Đã {$action} tài khoản {$user->name}!");
    }

    /**
     * Xóa người dùng
     */
    public function destroy(User $user)
    {
        // Chỉ cho phép xóa tài khoản khách hàng
        if ($user->role !== 'user') {
            return redirect()->route('admin.users.index')->with('error', 'Không thể xóa tài khoản admin!');
        }

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Đã xoá người dùng!');
    }
}
