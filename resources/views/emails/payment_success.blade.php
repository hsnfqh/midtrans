{{-- 
  =============================================================================
  [FILE BARU: TEMPLATE EMAIL STRUK KONFIRMASI PEMBAYARAN]
  File ini merender tampilan invoice / bukti pembayaran resmi untuk pelanggan.
  =============================================================================
--}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Konfirmasi Pembayaran #{{ $order->order_id }} - Kala Coffee</title>
  <style>
    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
      background-color: #f1f5f9;
      margin: 0;
      padding: 30px 15px;
      color: #1e293b;
      -webkit-text-size-adjust: 100%;
    }
    .email-container {
      max-width: 600px;
      margin: 0 auto;
      background-color: #ffffff;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
      border: 1px solid #e2e8f0;
    }
    .header {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
      padding: 32px 30px;
      text-align: center;
      color: #ffffff;
    }
    .brand-title {
      font-size: 22px;
      font-weight: 800;
      letter-spacing: -0.5px;
      margin: 0 0 6px 0;
      color: #38bdf8;
    }
    .brand-subtitle {
      font-size: 13px;
      color: #94a3b8;
      margin: 0;
    }
    .status-banner {
      background: #ecfdf5;
      border-bottom: 1px solid #a7f3d0;
      padding: 18px 30px;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
    }
    .status-badge {
      display: inline-block;
      background: #10b981;
      color: #ffffff;
      font-size: 12px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      padding: 5px 14px;
      border-radius: 9999px;
      margin-bottom: 6px;
    }
    .status-text {
      color: #065f46;
      font-size: 14px;
      font-weight: 600;
      margin: 0;
    }
    .content {
      padding: 30px;
    }
    .greeting {
      font-size: 16px;
      line-height: 1.6;
      margin-bottom: 24px;
      color: #334155;
    }
    .order-box {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 24px;
    }
    .order-grid {
      width: 100%;
      border-collapse: collapse;
      font-size: 13px;
    }
    .order-grid td {
      padding: 6px 0;
    }
    .order-grid .label {
      color: #64748b;
      width: 40%;
    }
    .order-grid .val {
      font-weight: 600;
      color: #0f172a;
      text-align: right;
    }
    .items-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 24px;
      font-size: 14px;
    }
    .items-table th {
      text-align: left;
      padding: 10px 0;
      border-bottom: 2px solid #e2e8f0;
      color: #475569;
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .items-table td {
      padding: 14px 0;
      border-bottom: 1px solid #f1f5f9;
    }
    .item-name {
      font-weight: 600;
      color: #0f172a;
    }
    .item-desc {
      font-size: 12px;
      color: #64748b;
      margin-top: 2px;
    }
    .total-section {
      border-top: 2px dashed #cbd5e1;
      padding-top: 16px;
      margin-top: 10px;
    }
    .total-row {
      display: flex;
      justify-content: space-between;
      font-size: 16px;
      font-weight: 800;
      color: #0f172a;
    }
    .footer {
      background: #f8fafc;
      border-top: 1px solid #e2e8f0;
      padding: 24px 30px;
      text-align: center;
      font-size: 12px;
      color: #64748b;
      line-height: 1.5;
    }
    .action-btn-group {
      text-align: center;
      margin: 24px 0 10px 0;
    }
    .btn-home {
      display: inline-block;
      background: #0f172a;
      color: #ffffff !important;
      text-decoration: none;
      font-size: 13px;
      font-weight: 700;
      padding: 10px 22px;
      border-radius: 8px;
    }
  </style>
</head>
<body>

  <div class="email-container">
    <!-- Header -->
    <div class="header">
      <div class="brand-title">☕ Kala Coffee Roastery</div>
      <div class="brand-subtitle">Notifikasi Pembayaran Resmi E-Commerce</div>
    </div>

    <!-- Status Banner -->
    <div class="status-banner">
      <div>
        <span class="status-badge">✓ PEMBAYARAN LUNAS</span>
        <p class="status-text">Terima kasih! Pembayaran Anda telah kami terima.</p>
      </div>
    </div>

    <!-- Content -->
    <div class="content">
      <p class="greeting">
        Halo <strong>{{ $order->customer_name }}</strong>,<br>
        Pesanan kopimu sedang kami siapkan untuk diproses dan dikirimkan. Berikut adalah rincian bukti transaksi pembayaranmu:
      </p>

      <!-- Order Summary Card -->
      <div class="order-box">
        <table class="order-grid">
          <tr>
            <td class="label">Nomor Pesanan (Order ID)</td>
            <td class="val"><code>{{ $order->order_id }}</code></td>
          </tr>
          <tr>
            <td class="label">Waktu Transaksi</td>
            <td class="val">{{ $order->created_at ? $order->created_at->format('d M Y, H:i') : now()->format('d M Y, H:i') }} WIB</td>
          </tr>
          <tr>
            <td class="label">Metode Pembayaran</td>
            <td class="val">{{ strtoupper($order->payment_type ?? 'MIDTRANS PAYMENT') }}</td>
          </tr>
          <tr>
            <td class="label">Email Pelanggan</td>
            <td class="val">{{ $order->customer_email }}</td>
          </tr>
          <tr>
            <td class="label">No. WhatsApp / HP</td>
            <td class="val">{{ $order->customer_phone }}</td>
          </tr>
        </table>
      </div>

      <!-- Total Bayar -->
      <div class="total-section">
        <div class="total-row">
          <span>Total Pembayaran</span>
          <span style="color: #059669;">Rp {{ number_format($order->gross_amount, 0, ',', '.') }}</span>
        </div>
      </div>

      <div class="action-btn-group">
        <a href="{{ route('payment.history') }}" class="btn-home">← Kembali ke Riwayat Pesanan</a>
      </div>
    </div>

    <!-- Footer -->
    <div class="footer">
      <p style="margin: 0 0 6px 0;"><strong>Kala Coffee Roastery Indonesia</strong></p>
      <p style="margin: 0;">Email ini dibuat secara otomatis oleh sistem simulasi e-commerce Laravel & Midtrans.</p>
    </div>
  </div>

</body>
</html>
