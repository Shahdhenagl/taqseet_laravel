@extends('layouts.app')

@section('content')
<header style="margin-bottom: 24px; padding-top: 20px;">
    <h1 style="color: var(--primary); margin-bottom: 8px;">نظام التقسيط والكاشير (Laravel)</h1>
    <p style="color: var(--text-secondary);">مرحباً بك، إليك ملخص اليوم</p>
</header>

<div class="card" style="background: linear-gradient(135deg, var(--primary), var(--primary-hover)); color: white;">
    <h3 style="margin-bottom: 8px; opacity: 0.9;">الأقساط المتأخرة والمستحقة</h3>
    <div style="font-size: 2.5rem; font-weight: bold;">{{ $pendingInstallmentsCount }}</div>
    <p style="margin-top: 8px; font-size: 0.875rem; opacity: 0.8;">أقساط تحتاج للمتابعة هذا الأسبوع</p>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
    <div class="card" style="margin-bottom: 0; text-align: center;">
        <h4 style="color: var(--text-secondary); margin-bottom: 8px; font-size: 0.875rem;">المنتجات</h4>
        <div style="font-size: 1.5rem; font-weight: bold; color: var(--primary);">{{ $productsCount }}</div>
    </div>
    <div class="card" style="margin-bottom: 0; text-align: center;">
        <h4 style="color: var(--text-secondary); margin-bottom: 8px; font-size: 0.875rem;">العملاء</h4>
        <div style="font-size: 1.5rem; font-weight: bold; color: var(--secondary);">{{ $customersCount }}</div>
    </div>
</div>

<h3 style="margin-bottom: 16px;">إجراءات سريعة</h3>
<div style="display: flex; flex-direction: column; gap: 12px;">
    <a href="{{ route('pos.index') }}" class="btn btn-primary">فاتورة كاشير جديدة</a>
    <a href="{{ route('products.index') }}" class="btn btn-secondary">إدارة المنتجات</a>
</div>
@endsection
