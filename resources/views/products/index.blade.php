@extends('layouts.app')

@section('content')
<header style="margin-bottom: 24px; padding-top: 20px; display: flex; justify-content: space-between; align-items: center;">
    <h1 style="color: var(--primary); margin: 0;">المنتجات والسلع</h1>
    <button onclick="document.getElementById('productForm').style.display='block'" class="btn btn-primary" style="width: auto; padding: 8px 16px;">+ إضافة</button>
</header>

<!-- Add Product Modal/Form -->
<div id="productForm" class="card" style="display: none; border: 2px solid var(--primary); margin-bottom: 24px;">
    <h3 style="margin-bottom: 16px;">إضافة منتج جديد</h3>
    <form action="{{ route('products.store') }}" method="POST">
        @csrf
        <div class="input-group">
            <label>اسم المنتج</label>
            <input type="text" name="name" class="input" required placeholder="مثال: دراجة نارية هوندا 150cc">
        </div>
        <div class="input-group">
            <label>الوصف</label>
            <textarea name="description" class="input" rows="2" placeholder="وصف قصير للمنتج..."></textarea>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            <div class="input-group">
                <label>السعر (ج.م)</label>
                <input type="number" name="price" step="0.01" class="input" required placeholder="35000">
            </div>
            <div class="input-group">
                <label>المخزون</label>
                <input type="number" name="stock" class="input" required placeholder="5">
            </div>
        </div>
        <div style="display: flex; gap: 12px; margin-top: 12px;">
            <button type="submit" class="btn btn-primary">حفظ المنتج</button>
            <button type="button" onclick="document.getElementById('productForm').style.display='none'" class="btn btn-secondary">إلغاء</button>
        </div>
    </form>
</div>

@if($products->isEmpty())
    <div class="card" style="text-align: center; padding: 40px 20px; color: var(--text-secondary);">
        لا توجد منتجات حالياً. أضف منتج جديد للبدء.
    </div>
@else
    <div style="display: flex; flex-direction: column; gap: 12px;">
        @foreach($products as $product)
            <div class="card" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0;">
                <div>
                    <h3 style="margin: 0; margin-bottom: 4px;">{{ $product->name }}</h3>
                    <p style="font-size: 0.875rem; color: var(--text-secondary);">المخزون المتوفر: {{ $product->stock }} قطعة</p>
                    @if($product->description)
                        <p style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 4px;">{{ $product->description }}</p>
                    @endif
                </div>
                <div style="font-weight: bold; color: var(--primary); font-size: 1.1rem;">
                    {{ number_format($product->price, 2) }} ج.م
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
