@extends('layouts.app')

@section('content')
<header style="margin-bottom: 24px; padding-top: 20px;">
    <h1 style="color: var(--primary); margin: 0;">نقطة البيع (الكاشير والتقسيط)</h1>
</header>

<form action="{{ route('pos.checkout') }}" method="POST" id="checkoutForm">
    @csrf
    
    <!-- Cart & Checkout Section -->
    <div class="card" style="border: 2px solid var(--primary);">
        <h3 style="margin-bottom: 16px;">سلة المشتريات وعقد الفاتورة</h3>

        <div id="cartItems" style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 16px;">
            <p id="emptyCartText" style="color: var(--text-secondary); text-align: center; margin: 20px 0;">السلة فارغة، اختر منتجات من الأسفل</p>
        </div>

        <div style="display: flex; justify-content: space-between; margin-top: 12px; font-size: 1.25rem; font-weight: bold; color: var(--primary); border-top: 1px solid var(--border); padding-top: 12px;">
            <span>الإجمالي:</span>
            <span id="cartTotal">0.00 ج.م</span>
        </div>

        <!-- Payment Type Selection -->
        <div style="margin-top: 16px;">
            <div class="input-group">
                <label>نوع الدفع</label>
                <select name="payment_type" id="paymentType" class="input" onchange="toggleInstallmentFields()">
                    <option value="cash">نقداً (كاش)</option>
                    <option value="installment">بيع بالتقسيط</option>
                </select>
            </div>

            <div id="installmentFields" style="display: none; background: var(--background); padding: 12px; border-radius: var(--radius-md); margin-bottom: 16px;">
                <div class="input-group">
                    <label>العميل</label>
                    <select name="customer_id" class="input">
                        <option value="">اختر العميل...</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone_number }})</option>
                        @endforeach
                    </select>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div class="input-group">
                        <label>المقدم (ج.م)</label>
                        <input type="number" name="down_payment" step="0.01" class="input" value="0">
                    </div>
                    <div class="input-group">
                        <label>عدد الأشهر</label>
                        <input type="number" name="months" class="input" value="6" min="1" max="36">
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" id="submitBtn" class="btn btn-primary" disabled>إتمام العملية</button>
    </div>
</form>

<!-- Products Available -->
<h3 style="margin-bottom: 16px;">المنتجات المتاحة</h3>
<div style="display: flex; flex-direction: column; gap: 12px;">
    @foreach($products as $product)
        <div class="card" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0; cursor: pointer;" onclick="addToCart('{{ $product->id }}', '{{ addslashes($product->name) }}', {{ $product->price }})">
            <div>
                <h4 style="margin: 0; margin-bottom: 4px;">{{ $product->name }}</h4>
                <p style="font-size: 0.875rem; color: var(--text-secondary);">المخزون المتوفر: {{ $product->stock }}</p>
            </div>
            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="font-weight: bold; color: var(--primary);">{{ number_format($product->price, 2) }} ج.م</div>
                <button type="button" style="background: var(--primary); color: white; border: none; border-radius: 50%; width: 32px; height: 32px; font-size: 1.2rem; cursor: pointer;">+</button>
            </div>
        </div>
    @endforeach
</div>

<script>
    let cart = {};

    function addToCart(id, name, price) {
        if (cart[id]) {
            cart[id].quantity += 1;
        } else {
            cart[id] = { id: id, name: name, price: price, quantity: 1 };
        }
        renderCart();
    }

    function renderCart() {
        const container = document.getElementById('cartItems');
        const emptyText = document.getElementById('emptyCartText');
        const submitBtn = document.getElementById('submitBtn');
        const totalSpan = document.getElementById('cartTotal');

        container.innerHTML = '';
        let total = 0;
        let keys = Object.keys(cart);

        if (keys.length === 0) {
            container.appendChild(emptyText);
            emptyText.style.display = 'block';
            submitBtn.disabled = true;
            totalSpan.innerText = '0.00 ج.م';
            return;
        }

        keys.forEach((key, index) => {
            let item = cart[key];
            total += item.price * item.quantity;

            let div = document.createElement('div');
            div.style.cssText = 'display: flex; justify-content: space-between; border-bottom: 1px solid var(--border); padding-bottom: 8px;';
            div.innerHTML = `
                <div>
                    <div style="font-weight: bold;">${item.name}</div>
                    <div style="font-size: 0.875rem; color: var(--text-secondary);">الكمية: ${item.quantity} × ${item.price} ج.م</div>
                    <input type="hidden" name="items[${index}][id]" value="${item.id}">
                    <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
                </div>
                <div style="font-weight: bold;">${(item.price * item.quantity).toFixed(2)} ج.م</div>
            `;
            container.appendChild(div);
        });

        totalSpan.innerText = total.toFixed(2) + ' ج.م';
        submitBtn.disabled = false;
    }

    function toggleInstallmentFields() {
        const type = document.getElementById('paymentType').value;
        const fields = document.getElementById('installmentFields');
        fields.style.display = (type === 'installment') ? 'block' : 'none';
    }
</script>
@endsection
