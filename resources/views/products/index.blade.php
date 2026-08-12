@extends('layouts.app')

@section('content')
<header style="margin-bottom: 20px; padding-top: 20px; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 style="color: var(--primary); margin: 0; margin-bottom: 4px;">المنتجات والسلع</h1>
        <p style="color: var(--text-secondary); margin: 0; font-size: 0.875rem;">إدارة كاثالوج المنتجات والمخزون</p>
    </div>
    <button onclick="document.getElementById('productForm').style.display='block'" class="btn btn-primary" style="width: auto; padding: 10px 18px;">+ إضافة منتج</button>
</header>

<!-- Product Search Bar -->
<div class="card" style="margin-bottom: 20px; padding: 12px 16px;">
    <form action="{{ route('products.index') }}" method="GET" style="display: flex; gap: 8px;">
        <div style="position: relative; flex: 1;">
            <input type="text" id="productSearchInput" name="search" class="input" value="{{ request('search') }}" 
                   placeholder="🔍 ابحث عن منتج بالاسم أو الوصف..." onkeyup="filterProductsLocal()">
        </div>
        @if(request('search'))
            <a href="{{ route('products.index') }}" class="btn btn-secondary" style="width: auto; padding: 10px 16px;">إلغاء البحث</a>
        @endif
    </form>
</div>

<!-- Add Product Form -->
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
                <label>المخزون المتوفر</label>
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
        لم يتم العثور على أي منتجات مضافة أو مطابقة للبحث.
    </div>
@else
    <div id="productsContainer" style="display: flex; flex-direction: column; gap: 12px;">
        @foreach($products as $product)
            <div class="card product-card" data-name="{{ strtolower($product->name) }}" data-desc="{{ strtolower($product->description ?? '') }}" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0; padding: 16px 20px;">
                <div>
                    <h3 style="margin: 0; margin-bottom: 4px; font-size: 1.1rem;">{{ $product->name }}</h3>
                    <p style="font-size: 0.875rem; color: var(--text-secondary); margin: 0;">📦 المخزون المتوفر: <strong>{{ $product->stock }}</strong> قطعة</p>
                    @if($product->description)
                        <p style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 4px; margin-bottom: 0;">{{ $product->description }}</p>
                    @endif
                </div>
                <div style="text-align: left;">
                    <div style="font-weight: bold; color: var(--primary); font-size: 1.2rem;">
                        {{ number_format($product->price, 2) }} ج.م
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

<script>
    function filterProductsLocal() {
        const input = document.getElementById('productSearchInput').value.toLowerCase();
        const cards = document.querySelectorAll('.product-card');
        
        cards.forEach(card => {
            const name = card.getAttribute('data-name');
            const desc = card.getAttribute('data-desc');
            if (name.includes(input) || desc.includes(input)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>
@endsection
