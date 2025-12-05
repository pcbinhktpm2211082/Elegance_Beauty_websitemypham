<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $categories = Category::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create'); 
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::create([
            'name' => $request->name,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm danh mục thành công!',
                'category' => $category
            ]);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Đã thêm danh mục!');
    }

    public function show(Category $category)
    {
        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update([
            'name' => $request->name,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật danh mục thành công!',
                'category' => $category->fresh()
            ]);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công!');
    }

    public function destroy(Category $category)
    {
        // Kiểm tra xem có sản phẩm nào đang sử dụng danh mục này không
        $productsCount = $category->products()->count();
        
        if ($productsCount > 0) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Không thể xóa danh mục này vì có {$productsCount} sản phẩm đang sử dụng."
                ], 422);
            }
            return redirect()->route('admin.categories.index')
                ->with('error', "Không thể xóa danh mục này vì có {$productsCount} sản phẩm đang sử dụng.");
        }

        $category->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Xóa danh mục thành công!'
            ]);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Xoá danh mục thành công!');
    }
}
