<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Riwayat Transaksi Toko & Notifikasi (MySQL Database)</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; color: #0f172a; padding-bottom: 60px; }
    .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
    
    .header { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 20px 0; margin-bottom: 24px; }
    .header-inner { display: flex; justify-content: space-between; align-items: center; }
    .brand { font-size: 18px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px; }
    .nav-btn { text-decoration: none; font-size: 13px; font-weight: 700; color: #0f766e; background: #ccfbf1; padding: 8px 16px; border-radius: 8px; transition: 0.2s; }
    .nav-btn:hover { background: #99f6e4; }

    .card { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .card-title { font-size: 18px; font-weight: 700; }
    
    .table-responsive { width: 100%; overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; }
    th { background: #f1f5f9; padding: 12px 14px; font-weight: 700; color: #475569; border-bottom: 2px solid #e2e8f0; white-space: nowrap; }
    td { padding: 14px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    tr:hover td { background: #fafafa; }
    
    .badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
    .badge-pending { background: #fef3c7; color: #b45309; }
    .badge-paid { background: #dcfce7; color: #15803d; }
    .badge-failed { background: #fee2e2; color: #b91c1c; }
    .badge-expired { background: #f1f5f9; color: #64748b; }
    
    .actions-cell { display: flex; flex-direction: column; gap: 6px; }
    .btn-action {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      font-size: 11px;
      font-weight: 700;
      padding: 6px 10px;
      border-radius: 6px;
      text-decoration: none;
      transition: all 0.15s ease;
      white-space: nowrap;
    }
    .btn-wa {
      background: #25d366;
      color: #ffffff;
    }
    .btn-wa:hover {
      background: #1eb857;
      transform: translateY(-1px);
    }
    .btn-email {
      background: #3b82f6;
      color: #ffffff;
    }
    .btn-email:hover {
      background: #2563eb;
      transform: translateY(-1px);
    }
    .btn-check {
      background: #e0f2fe;
      color: #0369a1;
    }
    .btn-check:hover {
      background: #bae6fd;
    }

    .empty-state { text-align: center; padding: 40px 0; color: #64748b; }
  </style>
</head>
<body>

  <header class="header">
    <div class="container header-inner">
      <div class="brand">
        <span>☕</span> Kala Coffee Roastery - Data Transaksi & Pengiriman
      </div>
      <a href="{{ route('payment.index') }}" class="nav-btn">+ Buat Pesanan Baru</a>
    </div>
  </header>

  <main class="container">
    <div class="card">
      <div class="card-header">
        <div>
          <h2 class="card-title">Daftar Transaksi (MySQL <code>orders</code>)</h2>
          <p style="font-size: 13px; color: #64748b; margin-top: 4px;">
            Daftar seluruh pesanan pelanggan, rincian kurir pengiriman, dan status pembayaran dari Midtrans.
          </p>
        </div>
        <button onclick="window.location.reload()" style="padding: 8px 14px; font-size: 13px; font-weight: 600; border: 1px solid #cbd5e1; background: #fff; border-radius: 8px; cursor: pointer;">
          🔄 Segarkan Halaman
        </button>
      </div>

      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>No</th>
              <th>Order ID</th>
              <th>Pelanggan</th>
              <!-- Kolom Pengiriman / Kurir -->
              <th>Pengiriman & Ongkir</th>
              <th>Total Bayar</th>
              <th>Metode</th>
              <th>Status</th>
              <th>Aksi & Notifikasi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($orders as $index => $order)
              @php
                // Format nomor HP ke standar WhatsApp internasional
                $cleanPhone = preg_replace('/[^0-9]/', '', $order->customer_phone);
                if (str_starts_with($cleanPhone, '0')) {
                    $cleanPhone = '62' . substr($cleanPhone, 1);
                }

                $nominalRp = number_format($order->gross_amount, 0, ',', '.');
                $shippingInfo = $order->shipping_courier ? "🚚 *Kurir:* {$order->shipping_courier} ({$order->shipping_service})\n" : "";
                
                $waMessage = "Halo Kak {$order->customer_name}! ☕\n\n"
                           . "Terima kasih banyak telah berbelanja di *Kala Coffee Roastery*.\n\n"
                           . "Berikut bukti konfirmasi pembayaranmu:\n"
                           . "📌 *Order ID:* {$order->order_id}\n"
                           . $shippingInfo
                           . "💰 *Total Bayar:* Rp {$nominalRp}\n"
                           . "💳 *Metode Bayar:* " . strtoupper($order->payment_type ?? 'Midtrans') . "\n"
                           . "✅ *Status:* SUDAH LUNAS (PAID)\n\n"
                           . "Pesanan kopimu sedang kami siapkan untuk segera dikirim! Have a great day! ✨";
                $waUrl = "https://api.whatsapp.com/send?phone={$cleanPhone}&text=" . urlencode($waMessage);
              @endphp
              <tr>
                <td>{{ $index + 1 }}</td>
                <td><strong><code>{{ $order->order_id }}</code></strong></td>
                <td>
                  <div style="font-weight: 700;">{{ $order->customer_name }}</div>
                  <small style="color: #64748b;">{{ $order->customer_email }}</small><br>
                  <small style="color: #334155; font-family: monospace;">{{ $order->customer_phone }}</small>
                </td>
                
                <!-- Menampilkan info kurir, ongkir, dan alamat tujuan -->
                <td>
                  @if($order->shipping_courier)
                    <div style="font-weight: 700; color: #0f172a;">{{ $order->shipping_courier }}</div>
                    <div style="font-size: 12px; color: #64748b;">
                      {{ $order->shipping_cost > 0 ? 'Rp ' . number_format($order->shipping_cost, 0, ',', '.') : 'Gratis Ongkir' }}
                    </div>
                    @if($order->shipping_address)
                      <div style="font-size: 11px; color: #94a3b8; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $order->shipping_address }}">
                        📍 {{ $order->shipping_address }}
                      </div>
                    @endif
                  @else
                    <span style="color: #94a3b8; font-style: italic; font-size: 12px;">Standard</span>
                  @endif
                </td>

                <td><strong>Rp {{ number_format($order->gross_amount, 0, ',', '.') }}</strong></td>
                <td>
                  @if($order->payment_type)
                    <span style="font-family: monospace; font-size: 11px; background: #f1f5f9; padding: 3px 6px; border-radius: 4px; font-weight: 700;">{{ strtoupper($order->payment_type) }}</span>
                  @else
                    <span style="color: #94a3b8; font-style: italic; font-size: 12px;">-</span>
                  @endif
                </td>
                <td>
                  @if($order->status === 'paid')
                    <span class="badge badge-paid">✓ Lunas</span>
                  @elseif($order->status === 'pending')
                    <span class="badge badge-pending">⏳ Pending</span>
                  @elseif($order->status === 'expired')
                    <span class="badge badge-expired">Expired</span>
                  @else
                    <span class="badge badge-failed">{{ ucfirst($order->status) }}</span>
                  @endif
                </td>
                <td>
                  <div class="actions-cell">
                    @if($order->status === 'paid')
                      <a href="{{ $waUrl }}" target="_blank" class="btn-action btn-wa" title="Buka WhatsApp">
                        <span>💬</span> Kirim Notif WA
                      </a>

                      <a href="{{ route('payment.emailPreview', $order->order_id) }}" target="_blank" class="btn-action btn-email" title="Lihat template email struk">
                        <span>📧</span> Lihat Struk Email
                      </a>
                    @elseif($order->status === 'pending')
                      <a href="{{ route('payment.checkStatus', $order->order_id) }}" class="btn-action btn-check">
                        <span>🔄</span> Cek Status Midtrans
                      </a>
                    @else
                      <span style="font-size: 12px; color: #94a3b8; font-style: italic;">Tidak ada aksi</span>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="empty-state">
                  <p style="font-size: 16px; font-weight: 600;">Belum ada data transaksi di database</p>
                  <p style="font-size: 13px; margin-top: 6px;">Silakan buat pesanan baru di halaman checkout untuk mulai mencoba.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </main>

</body>
</html>
