<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" href="data:,">
  <title>Simulasi Pembayaran Midtrans - Kala Coffee</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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

    /* Top Banner Sandbox */
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

    /* Header Navigasi */
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

    .brand-title h1 {
      font-size: 18px;
      font-weight: 700;
      color: var(--text-main);
      line-height: 1.2;
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

    /* Layout Grid */
    .main-content {
      padding-top: 32px;
      padding-bottom: 60px;
    }

    .checkout-grid {
      display: grid;
      grid-template-columns: 1fr 430px;
      gap: 28px;
      align-items: start;
    }

    @media (max-width: 880px) {
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

    /* Styling untuk Pilihan Kurir & Pengiriman */
    .shipping-options-list {
      display: flex;
      flex-direction: column;
      gap: 12px;
      margin-top: 14px;
    }

    .shipping-option-card {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 14px 16px;
      border: 2px solid var(--border-color);
      border-radius: var(--radius-md);
      cursor: pointer;
      background: #ffffff;
      transition: all 0.2s ease;
    }

    .shipping-option-card:hover {
      border-color: #94a3b8;
      background: #f8fafc;
    }

    .shipping-option-card.selected {
      border-color: var(--brand-accent);
      background: #f0fdfa;
      box-shadow: 0 2px 8px rgba(15, 118, 110, 0.12);
    }

    .shipping-left {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .shipping-icon {
      font-size: 24px;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #ffffff;
      border: 1px solid var(--border-color);
      border-radius: var(--radius-sm);
    }

    .shipping-title {
      font-size: 14px;
      font-weight: 700;
      color: var(--text-main);
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .shipping-badge {
      font-size: 10px;
      font-weight: 700;
      padding: 2px 6px;
      border-radius: 4px;
      background: #e0f2fe;
      color: #0369a1;
    }

    .shipping-service {
      font-size: 12px;
      color: var(--text-muted);
      margin-top: 2px;
    }

    .shipping-price {
      font-size: 14px;
      font-weight: 700;
      color: var(--brand-accent);
      text-align: right;
    }

    /* Rincian Pesanan & Ringkasan */
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

    /* Kupon Promo */
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
    }

    .coupon-tags {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 6px;
      margin-top: 8px;
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
    }

    /* [KODE BARU] Styling untuk Pilihan Cepat Catatan & Permintaan Khusus */
    .notes-quick-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-bottom: 12px;
    }

    .note-chip {
      font-size: 12px;
      font-weight: 600;
      padding: 6px 12px;
      background: #f8fafc;
      color: #334155;
      border: 1px solid #cbd5e1;
      border-radius: 20px;
      cursor: pointer;
      transition: all 0.15s ease;
      user-select: none;
    }

    .note-chip:hover {
      background: #e2e8f0;
      border-color: #94a3b8;
    }

    .note-chip.active {
      background: #0f766e;
      color: #ffffff;
      border-color: #0f766e;
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

    .total-price {
      font-size: 18px;
      font-weight: 800;
      color: var(--brand-accent);
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
    }

    .btn-pay:disabled {
      background: #94a3b8;
      cursor: not-allowed;
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

    /* Modal Struk */
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
  </style>

  <!-- Script Midtrans Snap Sandbox -->
  <script 
    src="{{ $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" 
    data-client-key="{{ $clientKey }}">
  </script>
</head>
<body>

  <!-- Header Navigasi -->
  <header class="store-header">
    <div class="container header-container">
      <div class="brand-title">
        <h1>Kala Coffee Roastery &bull; Checkout</h1>
      </div>
      <div style="display: flex; align-items: center; gap: 16px;">
        <a href="{{ route('payment.history') }}" style="text-decoration: none; font-size: 13px; font-weight: 600; color: #0f766e; background: #ccfbf1; padding: 6px 14px; border-radius: 6px; display: inline-flex; align-items: center; gap: 6px;">
          Lihat Riwayat Transaksi (Database)
        </a>
        <div class="header-secure">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
          <span>Midtrans Snap Ready</span>
        </div>
      </div>
    </div>
  </header>

  <!-- Konten Utama -->
  <main class="container main-content">
    <div class="checkout-grid">

      <!-- Kolom Kiri: Data Pembeli, Pengiriman & Opsi Kurir -->
      <section class="checkout-form-section">
        
        <!-- Step 1: Data Pembeli -->
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
          </form>
        </div>

        <!-- Step 2: Alamat Pengiriman & Pilihan Kurir -->
        <div class="card">
          <div class="card-header">
            <span class="step-num">2</span>
            <h2>Alamat Pengiriman & Pilihan Kurir</h2>
          </div>
          <p class="section-desc">Lengkapi alamat tujuan pengiriman dan pilih kurir yang diinginkan.</p>

          <div class="form-group" style="margin-bottom: 14px;">
            <label for="shippingAddress">Alamat Jalan & Patokan Lokasi</label>
            <textarea id="shippingAddress" class="form-control" rows="2" placeholder="Masukkan nama jalan, nomor rumah, RT/RW, patokan..." required>Jl. Merdeka No. 45 (Samping Coffee Lab)</textarea>
          </div>

          <!-- [KODE BARU] Input Kota/Kabupaten dan Kode Pos untuk melengkapi alamat pengiriman -->
          <div class="form-row" style="margin-bottom: 18px;">
            <div class="form-group">
              <label for="shippingCity">Kota / Kabupaten</label>
              <input type="text" id="shippingCity" class="form-control" placeholder="Contoh: Jakarta Selatan" value="Jakarta Selatan" required>
            </div>
            <div class="form-group">
              <label for="shippingPostalCode">Kode Pos</label>
              <input type="text" id="shippingPostalCode" class="form-control" placeholder="Contoh: 12190" value="12190">
            </div>
          </div>

          <label style="font-size: 13px; font-weight: 600; color: #334155; display: block; margin-bottom: 8px;">
            Pilih Ekspedisi / Kurir
          </label>

          <!-- Daftar Kartu Pilihan Kurir -->
          <div class="shipping-options-list" id="shippingOptionsContainer">
            @foreach($shippingOptions as $index => $ship)
              <div 
                class="shipping-option-card {{ $index === 0 ? 'selected' : '' }}" 
                data-id="{{ $ship['id'] }}"
                data-courier="{{ $ship['courier'] }}"
                data-service="{{ $ship['service'] }}"
                data-cost="{{ $ship['cost'] }}"
                onclick="selectShipping('{{ $ship['id'] }}')"
              >
                <div class="shipping-left">
                  <div class="shipping-icon">{{ $ship['icon'] }}</div>
                  <div>
                    <div class="shipping-title">
                      <span>{{ $ship['courier'] }}</span>
                      <span class="shipping-badge">{{ $ship['badge'] }}</span>
                    </div>
                    <div class="shipping-service">{{ $ship['service'] }}</div>
                  </div>
                </div>
                <div class="shipping-price">
                  {{ $ship['cost'] > 0 ? 'Rp ' . number_format($ship['cost'], 0, ',', '.') : 'GRATIS' }}
                </div>
              </div>
            @endforeach
          </div>
        </div>

        <!-- [KODE BARU] Step 3: Catatan & Pesan Khusus untuk Pesanan -->
        <div class="card">
          <div class="card-header">
            <span class="step-num">3</span>
            <h2>Catatan & Permintaan Khusus Pesanan</h2>
          </div>
          <p class="section-desc">Tambahkan permintaan gilingan kopi, tingkat manis, atau pesan ke kurir.</p>

          <!-- Pilihan Cepat Kategori Gilingan & Pesan -->
          <div class="notes-quick-tags">
            <span class="note-chip" onclick="addQuickNote('Biji Kopi Utuh (Whole Bean)')">☕ Biji Utuh</span>
            <span class="note-chip" onclick="addQuickNote('Giling Halus (Espresso/Mokapot)')">✨ Giling Halus</span>
            <span class="note-chip" onclick="addQuickNote('Giling Medium (V60/Filter)')">☕ Giling Medium</span>
            <span class="note-chip" onclick="addQuickNote('Giling Kasar (Cold Brew)')">🧊 Giling Kasar</span>
            <span class="note-chip" onclick="addQuickNote('Titip di pos satpam')">🚪 Titip Satpam</span>
          </div>

          <div class="form-group">
            <label for="orderNotes">Pesan / Catatan Tambahan (Opsional)</label>
            <textarea id="orderNotes" class="form-control" rows="2" placeholder="Contoh: Tolong digiling medium untuk seduh V60, terima kasih!"></textarea>
          </div>
        </div>

        <!-- Step 4: Metode Pembayaran Tersedia di Midtrans -->
        <div class="card">
          <div class="card-header">
            <span class="step-num">4</span>
            <h2>Metode Pembayaran Didukung</h2>
          </div>
          <p class="section-desc">Pilihan metode bayar aktif secara otomatis pada popup Midtrans Snap:</p>
          
          <div style="display: flex; flex-direction: column; gap: 10px;">
            <div style="display: flex; justify-content: space-between; background: #f8fafc; padding: 12px 16px; border-radius: 8px; font-size: 13px;">
              <span style="font-weight: 600;">Virtual Account (VA)</span>
              <span style="color: #64748b;">BCA, Mandiri, BNI, BRI, Permata</span>
            </div>
            <div style="display: flex; justify-content: space-between; background: #f8fafc; padding: 12px 16px; border-radius: 8px; font-size: 13px;">
              <span style="font-weight: 600;">E-Wallet & QRIS</span>
              <span style="color: #64748b;">GoPay, ShopeePay, QRIS Semua Bank</span>
            </div>
            <div style="display: flex; justify-content: space-between; background: #f8fafc; padding: 12px 16px; border-radius: 8px; font-size: 13px;">
              <span style="font-weight: 600;">Kartu Debit/Kredit</span>
              <span style="color: #64748b;">Visa, Mastercard, JCB (Mode Sandbox)</span>
            </div>
          </div>
        </div>

      </section>

      <!-- Kolom Kanan: Ringkasan Belanja & Kupon -->
      <aside class="checkout-summary-section">
        <div class="card summary-card">
          <div class="card-header">
            <span class="step-num">5</span>
            <h2>Ringkasan Pesanan</h2>
          </div>

          <!-- Daftar Item Produk -->
          <div class="cart-items" id="cartItemsList">
            <!-- Di-render oleh JavaScript -->
          </div>

          <!-- Kotak Kupon Promo -->
          <div class="coupon-box">
            <div class="coupon-header">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
              <span>Punya Kupon Promo?</span>
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

          <!-- Rincian Biaya & Ongkir -->
          <div class="pricing-breakdown">
            <div class="price-row">
              <span>Subtotal Produk</span>
              <span id="subtotalAmount">Rp 0</span>
            </div>
            
            <!-- Baris Ongkos Kirim Dinamis -->
            <div class="price-row">
              <span>Ongkos Kirim (<span id="shippingCourierLabel">GrabExpress</span>)</span>
              <span id="shippingCostAmount" style="font-weight: 600; color: #0f172a;">Rp 15.000</span>
            </div>

            <!-- Baris Diskon Kupon -->
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
                <span style="font-size: 11px; color: #64748b;">Termasuk Produk, Ongkir & Pajak</span>
              </div>
              <span class="total-price" id="grandTotalAmount">Rp 0</span>
            </div>
          </div>

          <!-- Tombol Bayar -->
          <button type="button" class="btn-pay" id="payButton">
            <span class="btn-pay-text">Bayar Sekarang &rarr;</span>
            <span class="btn-pay-spinner" style="display: none;">Menghubungkan Midtrans...</span>
          </button>

          <p class="security-footer">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            Terkoneksi langsung ke Midtrans Snap Sandbox
          </p>
        </div>
      </aside>

    </div>
  </main>

  <!-- Modal Struk Transaksi -->
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
    // Data produk dan opsi pengiriman dari controller Laravel
    const products = @json($products);
    const shippingOptions = @json($shippingOptions);
    const ADMIN_FEE = {{ $adminFee }};

    // State kupon dan pengiriman aktif
    let appliedCoupon = null;
    let selectedShipping = shippingOptions.length > 0 ? shippingOptions[0] : null;

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

    // Fungsi memilih kurir pengiriman
    window.selectShipping = function(shippingId) {
      const found = shippingOptions.find(s => s.id === shippingId);
      if (found) {
        selectedShipping = found;

        // Memperbarui styling card yang aktif
        document.querySelectorAll('.shipping-option-card').forEach(card => {
          if (card.getAttribute('data-id') === shippingId) {
            card.classList.add('selected');
          } else {
            card.classList.remove('selected');
          }
        });

        // Hitung ulang total belanja dan ongkir
        renderCart();
      }
    };

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

      // Menghitung potongan kupon
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

      // Menghitung biaya ongkir yang dipilih
      const shippingCost = selectedShipping ? selectedShipping.cost : 0;
      const courierLabel = selectedShipping ? selectedShipping.courier : 'Pilih Kurir';

      document.getElementById('shippingCourierLabel').textContent = courierLabel;
      document.getElementById('shippingCostAmount').textContent = shippingCost > 0 ? formatRupiah(shippingCost) : 'GRATIS';

      // Total akhir = Subtotal + Admin + Ongkir - Diskon Kupon
      const grandTotal = subtotal > 0 ? Math.max(1000, subtotal + shippingCost + ADMIN_FEE - discountAmount) : 0;

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

    async function handleApplyCoupon() {
      const couponInput = document.getElementById('couponInput');
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

    // [KODE BARU] Fungsi memilih / menambahkan opsi cepat catatan gilingan & pesan ke textarea
    window.addQuickNote = function(text) {
      const orderNotesInput = document.getElementById('orderNotes');
      if (!orderNotesInput) return;

      if (orderNotesInput.value.trim() === '') {
        orderNotesInput.value = text;
      } else {
        if (!orderNotesInput.value.includes(text)) {
          orderNotesInput.value += ', ' + text;
        }
      }
      orderNotesInput.focus();
    };

    // Handler proses checkout dan pembuatan snap token
    async function handlePaymentCheckout() {
      const customerName = document.getElementById('customerName').value.trim();
      const customerEmail = document.getElementById('customerEmail').value.trim();
      const customerPhone = document.getElementById('customerPhone').value.trim();
      const shippingAddress = document.getElementById('shippingAddress').value.trim();
      // [KODE BARU] Mengambil input detail kota, kode pos, dan catatan pesanan
      const shippingCity = document.getElementById('shippingCity').value.trim();
      const shippingPostalCode = document.getElementById('shippingPostalCode').value.trim();
      const orderNotes = document.getElementById('orderNotes').value.trim();

      if (!customerName || !customerEmail || !customerPhone || !shippingAddress || !shippingCity) {
        alert('Mohon lengkapi Nama, Email, No. HP, Alamat Jalan, dan Kota Pengiriman.');
        return;
      }

      const activeItems = products.filter(p => p.quantity > 0);
      if (activeItems.length === 0) {
        alert('Keranjang belanja Anda kosong.');
        return;
      }

      const subtotal = calculateSubtotal();
      const shippingCost = selectedShipping ? selectedShipping.cost : 0;
      
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

      const grandTotal = Math.max(1000, subtotal + shippingCost + ADMIN_FEE - discountAmount);

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

        // [KODE BARU] Mengirim request pembuatan Snap Token lengkap dengan alamat detail dan catatan
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
            shipping_address: shippingAddress,
            shipping_city: shippingCity,
            shipping_postal_code: shippingPostalCode,
            order_notes: orderNotes,
            shipping_courier: selectedShipping ? selectedShipping.courier : 'Kurir Standard',
            shipping_service: selectedShipping ? selectedShipping.service : 'Delivery',
            shipping_cost: shippingCost,
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
              subtitle: 'Terima kasih atas pesanan Anda. Kopi akan segera disiapkan dan dikirim.',
              orderId: result.order_id,
              paymentType: result.payment_type || 'Midtrans Sandbox',
              notes: orderNotes,
              destination: `${shippingCity} (${shippingPostalCode || '-'})`,
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
              notes: orderNotes,
              destination: `${shippingCity} (${shippingPostalCode || '-'})`,
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

    function showReceiptModal({ status, title, subtitle, orderId, paymentType, extra, notes, destination, amount }) {
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
          <span>Tujuan:</span>
          <span>${destination || '-'}</span>
        </div>
        <div class="receipt-row">
          <span>Kurir:</span>
          <span>${selectedShipping ? selectedShipping.courier : 'Kurir'}</span>
        </div>
        <div class="receipt-row">
          <span>Metode:</span>
          <span>${paymentType}</span>
        </div>
      `;

      if (notes) {
        html += `
          <div class="receipt-row">
            <span>Catatan:</span>
            <span style="font-style: italic; color: #0f766e;">"${notes}"</span>
          </div>
        `;
      }

      html += `
        <div class="receipt-row" style="margin-top: 6px; padding-top: 6px; border-top: 1px dashed #cbd5e1;">
          <span style="font-weight: 700; color: #0f172a;">Total Bayar:</span>
          <span style="font-weight: 800; color: #0f766e;">${formatRupiah(amount)}</span>
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
