<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" href="data:,">
  <title>Simulasi Pembayaran</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    :root {
      --bg-page: #f8fafc;
      --bg-card: #ffffff;
      --text-main: #0f172a;
      --text-muted: #64748b;
      --border-color: #e2e8f0;
      --border-focus: #2563eb;
      --brand-primary: #1e293b;
      --brand-accent: #0f766e;
      --brand-hover: #115e59;
      --success: #10b981;
      --radius-sm: 6px;
      --radius-md: 10px;
      --radius-lg: 14px;
      --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
      --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
    }

    body {
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background-color: var(--bg-page);
      color: var(--text-main);
      line-height: 1.5;
      min-height: 100vh;
    }

    .container {
      max-width: 1100px;
      margin: 0 auto;
      padding: 0 20px;
    }

    /* Top Banner */
    .sandbox-banner {
      background-color: #0f172a;
      color: #f1f5f9;
      font-size: 13px;
      border-bottom: 1px solid #1e293b;
    }

    .banner-inner {
      max-width: 1100px;
      margin: 0 auto;
      padding: 8px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 8px;
    }

    .banner-status {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .status-indicator {
      display: inline-block;
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background-color: var(--success);
      box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.25);
    }

    .badge-laravel {
      background: #ef4444;
      color: #ffffff;
      font-size: 11px;
      font-weight: 700;
      padding: 2px 8px;
      border-radius: 999px;
    }

    /* Header */
    .store-header {
      background: #ffffff;
      border-bottom: 1px solid var(--border-color);
      padding: 18px 0;
    }

    .header-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .brand-badge {
      background: #1e293b;
      color: #ffffff;
      font-weight: 700;
      font-size: 13px;
      letter-spacing: 1px;
      padding: 6px 10px;
      border-radius: var(--radius-sm);
    }

    .brand-title h1 {
      font-size: 18px;
      font-weight: 700;
      color: var(--text-main);
      line-height: 1.2;
    }

    .brand-sub {
      font-size: 12px;
      color: var(--text-muted);
    }

    .header-secure {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 13px;
      color: var(--brand-accent);
      background: #f0fdfa;
      border: 1px solid #ccfbf1;
      padding: 6px 12px;
      border-radius: 9999px;
      font-weight: 500;
    }

    /* Main Grid */
    .main-content {
      padding-top: 32px;
      padding-bottom: 60px;
    }

    .checkout-grid {
      display: grid;
      grid-template-columns: 1fr 420px;
      gap: 28px;
      align-items: start;
    }

    @media (max-width: 860px) {
      .checkout-grid {
        grid-template-columns: 1fr;
      }
    }

    .card {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-lg);
      padding: 24px;
      margin-bottom: 24px;
      box-shadow: var(--shadow-sm);
    }

    .card-header {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 6px;
    }

    .step-num {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 24px;
      height: 24px;
      background: #f1f5f9;
      color: var(--text-main);
      font-size: 12px;
      font-weight: 700;
      border-radius: 50%;
      border: 1px solid var(--border-color);
    }

    .card-header h2 {
      font-size: 16px;
      font-weight: 700;
      color: var(--text-main);
    }

    .section-desc {
      font-size: 13px;
      color: var(--text-muted);
      margin-bottom: 20px;
    }

    .form-body {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    @media (max-width: 560px) {
      .form-row {
        grid-template-columns: 1fr;
      }
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .form-group label {
      font-size: 13px;
      font-weight: 600;
      color: #334155;
    }

    .form-control {
      font-family: inherit;
      font-size: 14px;
      color: var(--text-main);
      padding: 10px 14px;
      border: 1px solid var(--border-color);
      border-radius: var(--radius-md);
      background: #ffffff;
      outline: none;
      transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .form-control:focus {
      border-color: var(--border-focus);
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .payment-channels-list {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .channel-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: #f8fafc;
      border: 1px solid #f1f5f9;
      padding: 12px 16px;
      border-radius: var(--radius-md);
      font-size: 13px;
    }

    .channel-badge {
      font-weight: 600;
      color: #0f172a;
    }

    .channel-names {
      color: var(--text-muted);
      font-size: 12px;
    }

    /* Summary Card */
    .summary-card {
      position: sticky;
      top: 24px;
    }

    .cart-items {
      display: flex;
      flex-direction: column;
      gap: 14px;
      padding-bottom: 18px;
      border-bottom: 1px solid var(--border-color);
      margin-bottom: 18px;
    }

    .cart-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
    }

    .item-info {
      display: flex;
      flex-direction: column;
      gap: 2px;
    }

    .item-name {
      font-size: 14px;
      font-weight: 600;
      color: var(--text-main);
    }

    .item-price {
      font-size: 13px;
      color: var(--text-muted);
    }

    .item-actions {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .btn-qty {
      width: 26px;
      height: 26px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #f1f5f9;
      border: 1px solid var(--border-color);
      border-radius: var(--radius-sm);
      font-size: 14px;
      font-weight: 600;
      color: var(--text-main);
      cursor: pointer;
      transition: all 0.15s ease;
    }

    .btn-qty:hover {
      background: #e2e8f0;
    }

    .qty-val {
      font-size: 13px;
      font-weight: 600;
      min-width: 18px;
      text-align: center;
    }

    .pricing-breakdown {
      display: flex;
      flex-direction: column;
      gap: 10px;
      padding-bottom: 20px;
      border-bottom: 1px solid var(--border-color);
      margin-bottom: 20px;
    }

    .price-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 13px;
      color: var(--text-muted);
    }

    .total-row {
      font-size: 14px;
      color: var(--text-main);
      margin-top: 6px;
      padding-top: 10px;
      border-top: 1px dashed var(--border-color);
    }

    .total-label {
      display: block;
      font-weight: 700;
      font-size: 15px;
    }

    .total-note {
      font-size: 11px;
      color: var(--text-muted);
    }

    .total-price {
      font-size: 18px;
      font-weight: 800;
      color: var(--brand-accent);
    }

    /* Coupon / Voucher Section */
    .coupon-box {
      margin: 16px 0;
      padding: 14px;
      background: #f8fafc;
      border: 1px dashed #cbd5e1;
      border-radius: var(--radius-md);
    }

    .coupon-header {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 12px;
      font-weight: 700;
      color: #334155;
      margin-bottom: 8px;
    }

    .coupon-input-group {
      display: flex;
      gap: 6px;
    }

    .coupon-input {
      flex: 1;
      font-family: inherit;
      font-size: 13px;
      font-weight: 600;
      text-transform: uppercase;
      padding: 8px 12px;
      border: 1px solid var(--border-color);
      border-radius: var(--radius-sm);
      outline: none;
      background: #ffffff;
      color: var(--text-main);
      transition: border-color 0.15s ease;
    }

    .coupon-input:focus {
      border-color: var(--brand-accent);
    }

    .btn-apply-coupon {
      padding: 8px 14px;
      background: #0f172a;
      color: #ffffff;
      font-size: 12px;
      font-weight: 600;
      border: none;
      border-radius: var(--radius-sm);
      cursor: pointer;
      transition: background 0.15s ease;
      white-space: nowrap;
    }

    .btn-apply-coupon:hover {
      background: #334155;
    }

    .coupon-tags {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 6px;
      margin-top: 8px;
    }

    .coupon-chip-label {
      font-size: 11px;
      color: var(--text-muted);
    }

    .coupon-chip {
      font-size: 11px;
      font-weight: 600;
      padding: 3px 8px;
      background: #e0f2fe;
      color: #0369a1;
      border: 1px solid #bae6fd;
      border-radius: 9999px;
      cursor: pointer;
      transition: all 0.15s ease;
    }

    .coupon-chip:hover {
      background: #bae6fd;
      transform: translateY(-1px);
    }

    .coupon-alert {
      font-size: 11px;
      margin-top: 8px;
      padding: 6px 10px;
      border-radius: var(--radius-sm);
      display: none;
      line-height: 1.4;
    }

    .coupon-alert.success {
      display: block;
      background: #ecfdf5;
      color: #065f46;
      border: 1px solid #a7f3d0;
    }

    .coupon-alert.error {
      display: block;
      background: #fef2f2;
      color: #991b1b;
      border: 1px solid #fecaca;
    }

    .discount-row {
      color: #059669 !important;
      font-weight: 600;
      display: none;
    }

    .discount-row.active {
      display: flex !important;
    }

    .btn-remove-coupon {
      background: none;
      border: none;
      color: #ef4444;
      font-size: 11px;
      cursor: pointer;
      margin-left: 6px;
      text-decoration: underline;
      padding: 0;
    }

    .btn-pay {
      width: 100%;
      background: var(--brand-accent);
      color: #ffffff;
      font-family: inherit;
      font-size: 15px;
      font-weight: 700;
      padding: 14px;
      border: none;
      border-radius: var(--radius-md);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 10px rgba(15, 118, 110, 0.25);
      transition: all 0.2s ease;
    }

    .btn-pay:hover {
      background: var(--brand-hover);
      box-shadow: 0 6px 14px rgba(15, 118, 110, 0.35);
      transform: translateY(-1px);
    }

    .btn-pay:disabled {
      background: #94a3b8;
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }

    .security-footer {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      font-size: 11px;
      color: var(--text-muted);
      margin-top: 14px;
    }

    /* Modal Receipt */
    .modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, 0.6);
      backdrop-filter: blur(4px);
      display: none;
      align-items: center;
      justify-content: center;
      padding: 20px;
      z-index: 1000;
    }

    .modal-overlay.active {
      display: flex;
    }

    .modal-card {
      background: #ffffff;
      border-radius: var(--radius-lg);
      width: 100%;
      max-width: 460px;
      box-shadow: var(--shadow-lg);
      padding: 28px;
      text-align: center;
    }

    .receipt-icon-wrapper {
      width: 56px;
      height: 56px;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 16px;
    }

    .receipt-icon-wrapper.success {
      background: #ecfdf5;
      color: #059669;
    }

    .receipt-icon-wrapper.pending {
      background: #fffbeb;
      color: #d97706;
    }

    .receipt-title {
      font-size: 18px;
      font-weight: 700;
      color: var(--text-main);
      margin-bottom: 4px;
    }

    .receipt-subtitle {
      font-size: 13px;
      color: var(--text-muted);
      margin-bottom: 20px;
    }

    .receipt-details {
      background: #f8fafc;
      border: 1px solid var(--border-color);
      border-radius: var(--radius-md);
      padding: 16px;
      text-align: left;
      font-size: 13px;
      display: flex;
      flex-direction: column;
      gap: 8px;
      margin-bottom: 20px;
    }

    .receipt-row {
      display: flex;
      justify-content: space-between;
    }

    .receipt-row span:first-child {
      color: var(--text-muted);
    }

    .receipt-row span:last-child {
      font-weight: 600;
      color: var(--text-main);
    }

    .btn-primary {
      width: 100%;
      background: var(--brand-primary);
      color: #ffffff;
      font-family: inherit;
      font-size: 14px;
      font-weight: 600;
      padding: 12px;
      border: none;
      border-radius: var(--radius-md);
      cursor: pointer;
    }

    .btn-primary:hover {
      background: #0f172a;
    }
  </style>

  <!-- Midtrans Snap JS Resmi Sandbox -->
  <script 
    src="{{ $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" 
    data-client-key="{{ $clientKey }}">
  </script>
</head>
<body>

  <!-- Navbar / Header Sederhana -->
  <header class="store-header">
    <div class="container header-container">
      <div class="brand">
        <div class="brand-title">
          <h1>Simulasi Pembayaran</h1>
        </div>
      </div>
      <div style="display: flex; align-items: center; gap: 16px;">
        <a href="{{ route('payment.history') }}" style="text-decoration: none; font-size: 13px; font-weight: 600; color: #0f766e; background: #ccfbf1; padding: 6px 14px; border-radius: 6px; display: inline-flex; align-items: center; gap: 6px;">
          Lihat Riwayat Transaksi (Database)
        </a>
        <div class="header-secure">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
          <span>Pembayaran Aman Midtrans Snap</span>
        </div>
      </div>
    </div>
  </header>

  <!-- Main Content Layout -->
  <main class="container main-content">
    <div class="checkout-grid">

      <!-- Kolom Kiri: Form Data Pembeli & Info Metode Bayar -->
      <section class="checkout-form-section">
        
        <!-- Kartu Data Pembeli -->
        <div class="card">
          <div class="card-header">
            <span class="step-num">1</span>
            <h2>Informasi Kontak Pembeli</h2>
          </div>
          <p class="section-desc">Data ini akan dikirimkan ke Midtrans sebagai rincian pelanggan.</p>
          
          <form id="customerForm" class="form-body" onsubmit="return false;">
            <div class="form-group">
              <label for="customerName">Nama Lengkap</label>
              <input type="text" id="customerName" class="form-control" placeholder="Contoh: Ahmad Pratama" value="Ahmad Syarif" required>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="customerEmail">Email</label>
                <input type="email" id="customerEmail" class="form-control" placeholder="nama@email.com" value="ahmad.buyer@example.com" required>
              </div>
              <div class="form-group">
                <label for="customerPhone">No. WhatsApp / HP</label>
                <input type="tel" id="customerPhone" class="form-control" placeholder="08123456789" value="081234567890" required>
              </div>
            </div>

            <div class="form-group">
              <label for="orderNotes">Catatan Pesanan (Opsional)</label>
              <input type="text" id="orderNotes" class="form-control" placeholder="Contoh: Giling halus untuk tubruk, packing rapi.">
            </div>
          </form>
        </div>

        <!-- Kartu Pilihan Metode Pembayaran Sandbox -->
        <div class="card">
          <div class="card-header">
            <span class="step-num">2</span>
            <h2>Metode Pembayaran Tersedia</h2>
          </div>
          <p class="section-desc">Pilihan metode bayar aktif secara otomatis pada popup Midtrans Snap:</p>
          
          <div class="payment-channels-list">
            <div class="channel-item">
              <span class="channel-badge">Virtual Account (VA)</span>
              <span class="channel-names">BCA, Mandiri, BNI, BRI, Permata</span>
            </div>
            <div class="channel-item">
              <span class="channel-badge">E-Wallet & QRIS</span>
              <span class="channel-names">GoPay, ShopeePay, QRIS Semua Bank</span>
            </div>
            <div class="channel-item">
              <span class="channel-badge">Kartu Debit/Kredit</span>
              <span class="channel-names">Visa, Mastercard, JCB (Mode Sandbox)</span>
            </div>
          </div>
        </div>

      </section>

      <!-- Kolom Rincian Produk & Keranjang -->
      <aside class="checkout-summary-section">
        <div class="card summary-card">
          <div class="card-header">
            <span class="step-num">3</span>
            <h2>Ringkasan Pesanan</h2>
          </div>

          <!-- Daftar Item Produk -->
          <div class="cart-items" id="cartItemsList">
            <!-- Rendered by JS -->
          </div>

          <!-- Input kupon voucher -->
          <div class="coupon-box">
            <div class="coupon-header">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
              <span>Punya Kupon / Kode Promo?</span>
            </div>
            <div class="coupon-input-group">
              <input type="text" id="couponInput" class="coupon-input" placeholder="Contoh: DISKON50" />
              <button type="button" id="btnApplyCoupon" class="btn-apply-coupon">Pakai</button>
            </div>
            <div class="coupon-tags">
              <span class="coupon-chip" onclick="quickApplyCoupon('DISKON50')">⚡ DISKON50</span>
              <span class="coupon-chip" onclick="quickApplyCoupon('KOPIGRATIS')">☕ KOPIGRATIS</span>
              <span class="coupon-chip" onclick="quickApplyCoupon('HEMAT10K')">🏷️ HEMAT10K</span>
            </div>
            <div id="couponAlert" class="coupon-alert"></div>
          </div>

          <div class="pricing-breakdown">
            <div class="price-row">
              <span>Subtotal Produk</span>
              <span id="subtotalAmount">Rp 0</span>
            </div>
            <div class="price-row discount-row" id="discountRow">
              <span>
                Diskon Kupon (<span id="appliedCouponCode"></span>)
                <button type="button" class="btn-remove-coupon" id="btnRemoveCoupon" title="Hapus kupon">[hapus]</button>
              </span>
              <span id="discountAmountText">-Rp 0</span>
            </div>
            <div class="price-row">
              <span>Biaya Layanan / Admin</span>
              <span id="adminFeeAmount">Rp 1.000</span>
            </div>
            <div class="price-row total-row">
              <div>
                <span class="total-label">Total Pembayaran</span>
                <span class="total-note">Termasuk PPN & Biaya Transaksi</span>
              </div>
              <span class="total-price" id="grandTotalAmount">Rp 0</span>
            </div>
          </div>

          <button type="button" class="btn-pay" id="payButton">
            <span class="btn-pay-text">Bayar Sekarang &rarr;</span>
            <span class="btn-pay-spinner" style="display: none;">Memproses Midtrans...</span>
          </button>

          <p class="security-footer">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            Terkoneksi langsung ke Midtrans Snap Sandbox
          </p>
        </div>
      </aside>

    </div>
  </main>

  <div class="modal-overlay" id="receiptModal">
    <div class="modal-card">
      <div class="receipt-icon-wrapper" id="receiptIconWrapper"></div>
      <h3 class="receipt-title" id="receiptTitle">Pembayaran Berhasil!</h3>
      <p class="receipt-subtitle" id="receiptSubtitle">Terima kasih, pesanan Anda telah tercatat.</p>

      <div class="receipt-details" id="receiptDetails"></div>

      <button type="button" class="btn-primary" id="closeReceiptBtn">Kembali ke Beranda</button>
    </div>
  </div>

  <script>
    const products = @json($products);
    const ADMIN_FEE = {{ $adminFee }};

    // State kupon aktif
    let appliedCoupon = null;

    function formatRupiah(amount) {
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
      }).format(amount);
    }

    function calculateSubtotal() {
      return products
        .filter(p => p.quantity > 0)
        .reduce((acc, curr) => acc + (curr.price * curr.quantity), 0);
    }

    function renderCart() {
      const cartContainer = document.getElementById('cartItemsList');
      if (!cartContainer) return;

      cartContainer.innerHTML = '';
      const subtotal = calculateSubtotal();

      products.forEach((prod, index) => {
        const itemEl = document.createElement('div');
        itemEl.className = 'cart-item';
        itemEl.innerHTML = `
          <div class="item-info">
            <span class="item-name">${prod.name}</span>
            <span class="item-price">${formatRupiah(prod.price)} / pcs</span>
          </div>
          <div class="item-actions">
            <button type="button" class="btn-qty" onclick="changeQuantity(${index}, -1)">&minus;</button>
            <span class="qty-val">${prod.quantity}</span>
            <button type="button" class="btn-qty" onclick="changeQuantity(${index}, 1)">&plus;</button>
          </div>
        `;
        cartContainer.appendChild(itemEl);
      });

      // Hitung diskon kupon
      let discountAmount = 0;
      if (appliedCoupon && subtotal > 0) {
        if (appliedCoupon.type === 'percent') {
          discountAmount = Math.round((appliedCoupon.value / 100) * subtotal);
          if (appliedCoupon.max_discount && discountAmount > appliedCoupon.max_discount) {
            discountAmount = appliedCoupon.max_discount;
          }
        } else {
          discountAmount = Math.min(appliedCoupon.value, subtotal);
        }
      } else if (subtotal === 0) {
        appliedCoupon = null;
      }

      const discountRow = document.getElementById('discountRow');
      if (appliedCoupon && discountAmount > 0) {
        discountRow.classList.add('active');
        document.getElementById('appliedCouponCode').textContent = appliedCoupon.code;
        document.getElementById('discountAmountText').textContent = '-' + formatRupiah(discountAmount);
      } else {
        discountRow.classList.remove('active');
      }

      const grandTotal = subtotal > 0 ? Math.max(1000, subtotal - discountAmount + ADMIN_FEE) : 0;

      document.getElementById('subtotalAmount').textContent = formatRupiah(subtotal);
      document.getElementById('adminFeeAmount').textContent = formatRupiah(subtotal > 0 ? ADMIN_FEE : 0);
      document.getElementById('grandTotalAmount').textContent = formatRupiah(grandTotal);

      const payBtn = document.getElementById('payButton');
      if (payBtn) {
        payBtn.disabled = subtotal <= 0;
        payBtn.querySelector('.btn-pay-text').textContent = `Bayar Sekarang (${formatRupiah(grandTotal)}) \u2192`;
      }
    }

    window.changeQuantity = function (index, delta) {
      if (products[index]) {
        const newQty = products[index].quantity + delta;
        if (newQty >= 0) {
          products[index].quantity = newQty;
          renderCart();
        }
      }
    };

    window.quickApplyCoupon = function (code) {
      document.getElementById('couponInput').value = code;
      handleApplyCoupon();
    };

    // Validasi kupon ke server
    async function handleApplyCoupon() {
      const couponInput = document.getElementById('couponInput');
      const couponAlert = document.getElementById('couponAlert');
      const code = couponInput.value.trim().toUpperCase();
      const subtotal = calculateSubtotal();

      if (!code) {
        showCouponAlert('Masukkan kode kupon terlebih dahulu.', 'error');
        return;
      }

      if (subtotal <= 0) {
        showCouponAlert('Pilih minimal 1 produk sebelum memasang kupon.', 'error');
        return;
      }

      const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

      try {
        const response = await fetch("{{ route('coupon.check') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            coupon_code: code,
            subtotal: subtotal
          })
        });

        const data = await response.json();

        if (response.ok && data.status === 'success') {
          appliedCoupon = {
            code: data.coupon_code,
            type: data.discount_type,
            value: data.discount_value,
            amount: data.discount_amount,
            max_discount: data.max_discount
          };
          showCouponAlert(data.message, 'success');
          renderCart();
        } else {
          showCouponAlert(data.message || 'Kupon tidak dapat digunakan.', 'error');
        }
      } catch (err) {
        showCouponAlert('Gagal memeriksa kupon: ' + err.message, 'error');
      }
    }

    function showCouponAlert(msg, type) {
      const alertEl = document.getElementById('couponAlert');
      alertEl.textContent = msg;
      alertEl.className = `coupon-alert ${type}`;
    }

    function removeCoupon() {
      appliedCoupon = null;
      document.getElementById('couponInput').value = '';
      const alertEl = document.getElementById('couponAlert');
      alertEl.className = 'coupon-alert';
      alertEl.textContent = '';
      renderCart();
    }

    document.addEventListener('DOMContentLoaded', () => {
      renderCart();

      const payButton = document.getElementById('payButton');
      const closeReceiptBtn = document.getElementById('closeReceiptBtn');
      const receiptModal = document.getElementById('receiptModal');
      const btnApplyCoupon = document.getElementById('btnApplyCoupon');
      const btnRemoveCoupon = document.getElementById('btnRemoveCoupon');
      const couponInput = document.getElementById('couponInput');

      payButton.addEventListener('click', handlePaymentCheckout);

      btnApplyCoupon.addEventListener('click', handleApplyCoupon);
      btnRemoveCoupon.addEventListener('click', removeCoupon);
      couponInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
          e.preventDefault();
          handleApplyCoupon();
        }
      });

      closeReceiptBtn.addEventListener('click', () => {
        receiptModal.classList.remove('active');
      });
    });

    async function handlePaymentCheckout() {
      const customerName = document.getElementById('customerName').value.trim();
      const customerEmail = document.getElementById('customerEmail').value.trim();
      const customerPhone = document.getElementById('customerPhone').value.trim();

      if (!customerName || !customerEmail || !customerPhone) {
        alert('Mohon lengkapi Nama, Email, dan Nomor WhatsApp Anda.');
        return;
      }

      const activeItems = products.filter(p => p.quantity > 0);
      if (activeItems.length === 0) {
        alert('Keranjang belanja Anda kosong.');
        return;
      }

      const subtotal = calculateSubtotal();
      let discountAmount = 0;
      if (appliedCoupon) {
        if (appliedCoupon.type === 'percent') {
          discountAmount = Math.round((appliedCoupon.value / 100) * subtotal);
          if (appliedCoupon.max_discount && discountAmount > appliedCoupon.max_discount) {
            discountAmount = appliedCoupon.max_discount;
          }
        } else {
          discountAmount = Math.min(appliedCoupon.value, subtotal);
        }
      }

      const grandTotal = Math.max(1000, subtotal - discountAmount + ADMIN_FEE);

      const itemDetails = activeItems.map(p => ({
        id: p.id,
        price: p.price,
        quantity: p.quantity,
        name: p.name
      }));

      itemDetails.push({
        id: 'FEE-ADMIN',
        price: ADMIN_FEE,
        quantity: 1,
        name: 'Biaya Layanan / Admin'
      });

      const payBtn = document.getElementById('payButton');
      const payText = payBtn.querySelector('.btn-pay-text');
      const paySpinner = payBtn.querySelector('.btn-pay-spinner');

      payBtn.disabled = true;
      payText.style.display = 'none';
      paySpinner.style.display = 'inline';

      try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const response = await fetch("{{ route('payment.snapToken') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            gross_amount: grandTotal,
            items: itemDetails,
            customer_name: customerName,
            customer_email: customerEmail,
            customer_phone: customerPhone,
            coupon_code: appliedCoupon ? appliedCoupon.code : null
          })
        });

        const data = await response.json();

        if (!response.ok || data.status !== 'success') {
          throw new Error(data.message || 'Gagal memproses pembayaran ke server Midtrans.');
        }

        window.snap.pay(data.snap_token, {
          onSuccess: function (result) {
            fetch(`{{ url('/orders') }}/${result.order_id}/check`, {
              headers: { 'Accept': 'application/json' }
            }).catch(e => console.log('Sync error:', e));

            showReceiptModal({
              status: 'success',
              title: 'Pembayaran Berhasil!',
              subtitle: 'Terima kasih atas pesanan Anda. Transaksi telah sukses di Midtrans Sandbox.',
              orderId: result.order_id,
              paymentType: result.payment_type || 'Midtrans Sandbox',
              amount: grandTotal
            });
            resetPayButton();
          },
          onPending: function (result) {
            let extraInfo = '';
            if (result.va_numbers && result.va_numbers.length > 0) {
              extraInfo = `${result.va_numbers[0].bank.toUpperCase()} VA: ${result.va_numbers[0].va_number}`;
            } else if (result.bill_key) {
              extraInfo = `Mandiri Bill: ${result.bill_key} / ${result.biller_code}`;
            }

            showReceiptModal({
              status: 'pending',
              title: 'Menunggu Pembayaran',
              subtitle: 'Silakan selesaikan pembayaran di simulator Sandbox atau aplikasi terkait.',
              orderId: result.order_id,
              paymentType: result.payment_type || 'Virtual Account / QRIS',
              extra: extraInfo,
              amount: grandTotal
            });
            resetPayButton();
          },
          onError: function (result) {
            alert('Pembayaran gagal atau ditolak oleh sistem Midtrans.');
            resetPayButton();
          },
          onClose: function () {
            resetPayButton();
          }
        });

      } catch (err) {
        alert(err.message || 'Terjadi kesalahan saat checkout.');
        resetPayButton();
      }
    }

    function resetPayButton() {
      const payBtn = document.getElementById('payButton');
      if (payBtn) {
        payBtn.disabled = false;
        payBtn.querySelector('.btn-pay-text').style.display = 'inline';
        payBtn.querySelector('.btn-pay-spinner').style.display = 'none';
      }
    }

    function showReceiptModal({ status, title, subtitle, orderId, paymentType, extra, amount }) {
      const receiptModal = document.getElementById('receiptModal');
      const iconWrapper = document.getElementById('receiptIconWrapper');
      const titleEl = document.getElementById('receiptTitle');
      const subEl = document.getElementById('receiptSubtitle');
      const detailsEl = document.getElementById('receiptDetails');

      iconWrapper.className = `receipt-icon-wrapper ${status}`;
      if (status === 'success') {
        iconWrapper.innerHTML = `
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"></path></svg>
        `;
      } else {
        iconWrapper.innerHTML = `
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        `;
      }

      titleEl.textContent = title;
      subEl.textContent = subtitle;

      let html = `
        <div class="receipt-row">
          <span>Order ID:</span>
          <span>${orderId}</span>
        </div>
        <div class="receipt-row">
          <span>Metode:</span>
          <span>${paymentType}</span>
        </div>
        <div class="receipt-row">
          <span>Total Bayar:</span>
          <span>${formatRupiah(amount)}</span>
        </div>
      `;

      if (extra) {
        html += `
          <div class="receipt-row">
            <span>No. Rekening / VA:</span>
            <span style="color: #0f766e; font-weight: 700;">${extra}</span>
          </div>
        `;
      }

      detailsEl.innerHTML = html;
      receiptModal.classList.add('active');
    }
  </script>
</body>
</html>
