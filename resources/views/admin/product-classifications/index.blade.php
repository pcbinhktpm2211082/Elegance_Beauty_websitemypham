@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6">
    <!-- Tiêu đề -->
    <h1 class="text-xl font-bold text-center mb-2">Quản lý nhãn phân loại</h1>

    <!-- Nút thêm -->
    <div class="mb-4 text-left flex gap-2">
        <button onclick="openAddModal()" 
                class="inline-block px-4 py-2 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-sm font-semibold">
            + Thêm nhãn phân loại
        </button>
        <button onclick="openAddProductTypeModal()" 
                class="inline-block px-4 py-2 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-sm font-semibold">
            + Thêm loại sản phẩm
        </button>
    </div>

    <!-- Bảng -->
    <div class="bg-white p-4 rounded shadow border overflow-x-auto">
        <!-- Loại da -->
        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-3 text-gray-800">Loại da</h2>
            <table class="w-full table-auto border-collapse border border-gray-300 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2">ID</th>
                        <th class="border px-4 py-2">Tên nhãn</th>
                        <th class="border px-4 py-2">Số sản phẩm</th>
                        <th class="border px-4 py-2">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classifications['skin_type'] ?? [] as $classification)
                    <tr class="hover:bg-gray-50">
                        <td class="border px-4 py-2 text-center">{{ $classification->id }}</td>
                        <td class="border px-4 py-2">{{ $classification->name }}</td>
                        <td class="border px-4 py-2 text-center">{{ $classification->products()->count() }}</td>
                        <td class="border px-4 py-2">
                            <div class="flex justify-center gap-2 items-center">
                                <button onclick="openEditModal({{ $classification->id }}, '{{ $classification->name }}', '{{ $classification->type }}')" 
                                        class="px-3 py-1 min-h-[28px] bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-xs font-medium whitespace-nowrap">
                                    Sửa
                                </button>
                                <form method="POST" action="{{ route('admin.product-classifications.destroy', $classification->id) }}" 
                                      onsubmit="return confirm('Bạn có chắc muốn xoá?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 min-h-[28px] bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-xs font-medium whitespace-nowrap">
                                        Xóa
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="border px-4 py-2 text-center text-gray-500">Chưa có nhãn phân loại</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Các vấn đề da -->
        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-3 text-gray-800">Các vấn đề da</h2>
            <table class="w-full table-auto border-collapse border border-gray-300 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2">ID</th>
                        <th class="border px-4 py-2">Tên nhãn</th>
                        <th class="border px-4 py-2">Số sản phẩm</th>
                        <th class="border px-4 py-2">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classifications['skin_concern'] ?? [] as $classification)
                    <tr class="hover:bg-gray-50">
                        <td class="border px-4 py-2 text-center">{{ $classification->id }}</td>
                        <td class="border px-4 py-2">{{ $classification->name }}</td>
                        <td class="border px-4 py-2 text-center">{{ $classification->products()->count() }}</td>
                        <td class="border px-4 py-2">
                            <div class="flex justify-center gap-2 items-center">
                                <button onclick="openEditModal({{ $classification->id }}, '{{ $classification->name }}', '{{ $classification->type }}')" 
                                        class="px-3 py-1 min-h-[28px] bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-xs font-medium whitespace-nowrap">
                                    Sửa
                                </button>
                                <form method="POST" action="{{ route('admin.product-classifications.destroy', $classification->id) }}" 
                                      onsubmit="return confirm('Bạn có chắc muốn xoá?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 min-h-[28px] bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-xs font-medium whitespace-nowrap">
                                        Xóa
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="border px-4 py-2 text-center text-gray-500">Chưa có nhãn phân loại</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Loại sản phẩm -->
        <div class="mt-8 border-t pt-6">
            <h2 class="text-lg font-semibold mb-3 text-gray-800">Loại sản phẩm</h2>
            <table class="w-full table-auto border-collapse border border-gray-300 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2">ID</th>
                        <th class="border px-4 py-2">Tên loại sản phẩm</th>
                        <th class="border px-4 py-2">Cần lọc loại da</th>
                        <th class="border px-4 py-2">Số sản phẩm</th>
                        <th class="border px-4 py-2">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productTypes as $productType)
                    <tr class="hover:bg-gray-50">
                        <td class="border px-4 py-2 text-center">{{ $productType->id }}</td>
                        <td class="border px-4 py-2">{{ $productType->name }}</td>
                        <td class="border px-4 py-2 text-center">
                            @if($productType->requires_skin_type_filter)
                                <span class="text-green-600 font-semibold">Có</span>
                            @else
                                <span class="text-red-600 font-semibold">Không</span>
                            @endif
                        </td>
                        <td class="border px-4 py-2 text-center">
                            {{ \App\Models\Product::where('product_type', $productType->name)->count() }}
                        </td>
                        <td class="border px-4 py-2">
                            <div class="flex justify-center gap-2 items-center">
                                <button onclick="openEditProductTypeModal({{ $productType->id }}, '{{ $productType->name }}', {{ $productType->requires_skin_type_filter ? 'true' : 'false' }})" 
                                        class="px-3 py-1 min-h-[28px] bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-xs font-medium whitespace-nowrap">
                                    Sửa
                                </button>
                                <form method="POST" action="{{ route('admin.product-classifications.destroy-product-type', $productType->id) }}" 
                                      onsubmit="return confirm('Bạn có chắc muốn xoá?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 min-h-[28px] bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-xs font-medium whitespace-nowrap">
                                        Xóa
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="border px-4 py-2 text-center text-gray-500">Chưa có loại sản phẩm nào</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal thêm/sửa nhãn phân loại -->
<div id="classificationModal" class="fixed inset-0 bg-black bg-opacity-60 hidden z-[9999] flex items-center justify-center" style="backdrop-filter: blur(2px);">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 shadow-2xl border-2 border-gray-200" style="z-index: 10000;">
        <h2 id="modalTitle" class="text-xl font-bold mb-4 text-gray-900">Thêm nhãn phân loại</h2>
        
        <form id="classificationForm" method="POST">
            @csrf
            <div id="methodField"></div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-800 mb-2">Tên nhãn:</label>
                <input type="text" name="name" id="classificationName" required
                       class="w-full border-2 border-gray-300 rounded px-3 py-2 text-sm text-gray-900 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-800 mb-2">Loại:</label>
                <select name="type" id="classificationType" required
                        class="w-full border-2 border-gray-300 rounded px-3 py-2 text-sm text-gray-900 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="skin_type">Loại da</option>
                    <option value="skin_concern">Các vấn đề da</option>
                </select>
            </div>
            
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition font-medium border border-gray-400">
                    Hủy
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition font-medium shadow-md">
                    Lưu
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal thêm/sửa loại sản phẩm -->
<div id="productTypeModal" class="fixed inset-0 bg-black bg-opacity-60 hidden z-[9999] flex items-center justify-center" style="backdrop-filter: blur(2px);">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 shadow-2xl border-2 border-gray-200" style="z-index: 10000;">
        <h2 id="productTypeModalTitle" class="text-xl font-bold mb-4 text-gray-900">Thêm loại sản phẩm</h2>
        
        <form id="productTypeForm" method="POST">
            @csrf
            <div id="productTypeMethodField"></div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-800 mb-2">Tên loại sản phẩm:</label>
                <input type="text" name="name" id="productTypeName" required
                       class="w-full border-2 border-gray-300 rounded px-3 py-2 text-sm text-gray-900 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="VD: Exfoliator, Tẩy tế bào chết">
            </div>
            
            <div class="mb-4">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="requires_skin_type_filter" id="productTypeRequiresFilter" value="1" 
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
                    <span class="ml-2 text-sm font-semibold text-gray-800">
                        Cần áp dụng bộ lọc loại da (Da Dầu/Khô)
                    </span>
                </label>
                <p class="text-xs text-gray-500 mt-1 ml-6">
                    Nếu bỏ chọn, sản phẩm thuộc loại này sẽ bỏ qua bộ lọc loại da trong hệ thống gợi ý.
                </p>
            </div>
            
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeProductTypeModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition font-medium border border-gray-400">
                    Hủy
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition font-medium shadow-md">
                    Lưu
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Thêm nhãn phân loại';
    document.getElementById('classificationForm').action = '{{ route("admin.product-classifications.store") }}';
    document.getElementById('methodField').innerHTML = '';
    document.getElementById('classificationName').value = '';
    document.getElementById('classificationType').value = 'skin_type';
    document.getElementById('classificationModal').classList.remove('hidden');
}

function openEditModal(id, name, type) {
    document.getElementById('modalTitle').textContent = 'Sửa nhãn phân loại';
    document.getElementById('classificationForm').action = '{{ route("admin.product-classifications.update", ":id") }}'.replace(':id', id);
    document.getElementById('methodField').innerHTML = '@method("PUT")';
    document.getElementById('classificationName').value = name;
    document.getElementById('classificationType').value = type;
    document.getElementById('classificationModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('classificationModal').classList.add('hidden');
}

// Đóng modal khi click outside
document.getElementById('classificationModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Product Type Modal functions
function openAddProductTypeModal() {
    document.getElementById('productTypeModalTitle').textContent = 'Thêm loại sản phẩm';
    document.getElementById('productTypeForm').action = '{{ route("admin.product-classifications.store-product-type") }}';
    document.getElementById('productTypeMethodField').innerHTML = '';
    document.getElementById('productTypeName').value = '';
    document.getElementById('productTypeRequiresFilter').checked = true;
    document.getElementById('productTypeModal').classList.remove('hidden');
}

function openEditProductTypeModal(id, name, requiresFilter) {
    document.getElementById('productTypeModalTitle').textContent = 'Sửa loại sản phẩm';
    document.getElementById('productTypeForm').action = '{{ route("admin.product-classifications.update-product-type", ":id") }}'.replace(':id', id);
    document.getElementById('productTypeMethodField').innerHTML = '@method("PUT")';
    document.getElementById('productTypeName').value = name;
    document.getElementById('productTypeRequiresFilter').checked = requiresFilter === 'true' || requiresFilter === true;
    document.getElementById('productTypeModal').classList.remove('hidden');
}

function closeProductTypeModal() {
    document.getElementById('productTypeModal').classList.add('hidden');
}

// Đóng modal khi click outside
document.getElementById('productTypeModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeProductTypeModal();
    }
});
</script>
@endsection

