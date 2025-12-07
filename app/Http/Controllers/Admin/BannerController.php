<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('order')->paginate(10);
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|max:2048',
            'link' => 'nullable|url',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'position' => ['required', Rule::in(['left','right_top','right_bottom'])],
        ]);

        Log::info('Banner store request', [
            'position_in_request' => $request->input('position'),
            'validated' => $validated,
        ]);

        // Ensure only one active banner per right position (right_top, right_bottom)
        if (in_array($validated['position'], ['right_top','right_bottom'], true) && $request->boolean('is_active')) {
            $exists = Banner::where('position', $validated['position'])
                ->where('is_active', true)
                ->exists();
            if ($exists) {
                return back()->withInput()->withErrors([
                    'position' => 'Đã tồn tại banner đang hiển thị cho vị trí này. Vui lòng ẩn hoặc đổi vị trí banner cũ.'
                ]);
            }
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('banners', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        // Create explicitly to avoid any mass-assignment edge cases
        $banner = new Banner();
        $banner->title = $validated['title'] ?? null;
        $banner->description = $validated['description'] ?? null;
        $banner->image = $validated['image'] ?? null;
        $banner->link = $validated['link'] ?? null;
        $banner->order = (int)($validated['order'] ?? 0);
        $banner->is_active = (bool)$validated['is_active'];
        $banner->position = $validated['position'];
        $banner->save();
        $created = $banner->fresh();
        Log::info('Banner stored', [
            'id' => $created->id,
            'position_saved' => $created->position,
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner đã được tạo thành công!');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        // Validation rules cơ bản
        $rules = [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'link' => 'nullable|url',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'position' => ['required', Rule::in(['left','right_top','right_bottom'])],
            'image' => 'nullable|sometimes|image|max:2048',
        ];

        $validated = $request->validate($rules, [
            'image.image' => 'File phải là hình ảnh',
            'image.max' => 'Kích thước file không được vượt quá 2MB',
        ]);

        Log::info('Banner update request', [
            'banner_id' => $banner->id,
            'position_in_request' => $request->input('position'),
            'has_image' => $request->hasFile('image'),
            'image_valid' => $request->hasFile('image') ? $request->file('image')->isValid() : false,
            'validated' => $validated,
        ]);

        // Ensure only one active banner per right position on update
        if (in_array($validated['position'], ['right_top','right_bottom'], true) && $request->boolean('is_active')) {
            $exists = Banner::where('position', $validated['position'])
                ->where('is_active', true)
                ->where('id', '!=', $banner->id)
                ->exists();
            if ($exists) {
                return back()->withInput()->withErrors([
                    'position' => 'Đã tồn tại banner đang hiển thị cho vị trí này. Vui lòng ẩn hoặc đổi vị trí banner cũ.'
                ]);
            }
        }

        // Chỉ xử lý ảnh nếu có file mới được upload
        if ($request->hasFile('image')) {
            try {
                $file = $request->file('image');
                
                // Kiểm tra file hợp lệ
                if (!$file->isValid()) {
                    return back()->withInput()->withErrors([
                        'image' => 'File không hợp lệ. Vui lòng chọn file khác.'
                    ]);
                }

                // Xóa ảnh cũ
                if ($banner->image) {
                    Storage::disk('public')->delete($banner->image);
                }
                
                // Lưu ảnh mới
                $banner->image = $file->store('banners', 'public');
                Log::info('Banner image updated', [
                    'banner_id' => $banner->id,
                    'new_image_path' => $banner->image,
                    'file_size' => $file->getSize(),
                    'file_mime' => $file->getMimeType(),
                ]);
            } catch (\Exception $e) {
                Log::error('Error updating banner image', [
                    'banner_id' => $banner->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                return back()->withInput()->withErrors([
                    'image' => 'Có lỗi xảy ra khi lưu ảnh: ' . $e->getMessage()
                ]);
            }
        }

        $banner->title = $validated['title'] ?? null;
        $banner->description = $validated['description'] ?? null;
        $banner->link = $validated['link'] ?? null;
        $banner->order = (int)($validated['order'] ?? 0);
        $banner->is_active = $request->has('is_active');
        $banner->position = $validated['position'];
        $banner->save();
        
        $saved = $banner->fresh();
        Log::info('Banner updated', [
            'id' => $saved->id,
            'position_saved' => $saved->position,
            'image_path' => $saved->image,
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner đã được cập nhật thành công!');
    }

    public function destroy(Banner $banner)
    {
        // Xóa ảnh
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Banner đã được xóa thành công!');
    }
}
