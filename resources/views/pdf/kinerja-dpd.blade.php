<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family: DejaVu Sans, sans-serif; font-size:11px; color:#1e293b; background:#fff; padding:20px; }
  h1 { font-size:18px; font-weight:700; color:#0f172a; }
  .subtitle { font-size:12px; color:#64748b; margin-top:4px; margin-bottom:20px; }
  .metrics { display:flex; gap:12px; margin-bottom:20px; }
  .metric-box { flex:1; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px; }
  .metric-label { font-size:10px; color:#64748b; text-transform:uppercase; font-weight:600; }
  .metric-val { font-size:20px; font-weight:700; color:#0f172a; margin-top:4px; }
  .metric-sub { font-size:10px; color:#94a3b8; margin-top:2px; }
  table { width:100%; border-collapse:collapse; margin-top:12px; }
  th { background:#fe5000; color:white; padding:8px 10px; font-size:10px; text-transform:uppercase; letter-spacing:.5px; text-align:left; }
  td { padding:8px 10px; border-bottom:1px solid #f1f5f9; font-size:11px; }
  tr:nth-child(even) td { background:#f8fafc; }
  .badge { display:inline-block; padding:2px 8px; border-radius:99px; font-size:10px; font-weight:600; }
  .footer { margin-top:24px; font-size:10px; color:#94a3b8; text-align:right; }
  .section-title { font-size:13px; font-weight:700; color:#0f172a; margin-bottom:8px; margin-top:20px; border-left:4px solid #fe5000; padding-left:10px; }
</style>
</head>
<body>
  <h1>Laporan Kinerja DPD</h1>
  <div class="subtitle">
    Tahun {{  }}{{  ? ' &bull; Bulan ' . date('F', mktime(0,0,0,(int),1)) : '' }}
    &nbsp;&bull;&nbsp; Dicetak: {{ now()->format('d/m/Y H:i') }}
  </div>

  {{-- Ringkasan Utama --}}
  <table style="width:100%;border-collapse:collapse;margin-bottom:20px;">
    <tr>
      <td style="width:25%;padding:10px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;">
        <div style="font-size:10px;color:#64748b;font-weight:600;text-transform:uppercase;">Total Program</div>
        <div style="font-size:22px;font-weight:700;color:#0f172a;margin-top:4px;">{{ ['total_program'] }}</div>
        <div style="font-size:10px;color:#94a3b8;">Selesai: {{ ['total_selesai'] }}</div>
      </td>
      <td style="width:5%;"></td>
      <td style="width:25%;padding:10px;background:#f8fafc;border:1px solid #e2e8f0;">
        <div style="font-size:10px;color:#64748b;font-weight:600;text-transform:uppercase;">Penyelesaian</div>
        <div style="font-size:22px;font-weight:700;color:#10b981;margin-top:4px;">{{ ['persen_selesai'] }}%</div>
        <div style="font-size:10px;color:#94a3b8;">Dari total program</div>
      </td>
      <td style="width:5%;"></td>
      <td style="width:25%;padding:10px;background:#f8fafc;border:1px solid #e2e8f0;">
        <div style="font-size:10px;color:#64748b;font-weight:600;text-transform:uppercase;">Total RAB</div>
        <div style="font-size:16px;font-weight:700;color:#0f172a;margin-top:4px;">Rp {{ number_format(['total_rab'],0,',','.') }}</div>
        <div style="font-size:10px;color:#94a3b8;">Serapan: {{ ['persen_serapan'] }}%</div>
      </td>
      <td style="width:5%;"></td>
      <td style="width:25%;padding:10px;background:#f8fafc;border:1px solid #e2e8f0;">
        <div style="font-size:10px;color:#64748b;font-weight:600;text-transform:uppercase;">Total Peserta</div>
        <div style="font-size:22px;font-weight:700;color:#0f172a;margin-top:4px;">{{ number_format(['total_peserta'],0,',','.') }}</div>
        <div style="font-size:10px;color:#94a3b8;">Orang hadir</div>
      </td>
    </tr>
  </table>

  {{-- Rekap Per Bidang --}}
  <div class="section-title">Rekap Per Bidang</div>
  <table>
    <thead>
      <tr>
        <th style="width:5%;">No</th>
        <th style="width:30%;">Bidang</th>
        <th style="width:12%;text-align:center;">Total Program</th>
        <th style="width:12%;text-align:center;">Selesai</th>
        <th style="width:12%;text-align:center;">% Selesai</th>
        <th style="width:15%;text-align:right;">RAB (Rp)</th>
        <th style="width:15%;text-align:right;">Realisasi (Rp)</th>
        <th style="width:10%;text-align:center;">Peserta</th>
      </tr>
    </thead>
    <tbody>
      @php  = 1; @endphp
      @forelse ( as  => )
        @if(['program_total'] > 0 || true)
        @php
           = ['program_total'] > 0 ? round((['program_selesai'] / ['program_total']) * 100, 1) : 0;
        @endphp
        <tr>
          <td style="text-align:center;">{{ ++ }}</td>
          <td>
            <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:{{ ['color'] }};margin-right:6px;"></span>
            {{ ['nama'] }}
            @if(['singkatan']) <span style="color:#94a3b8;">({{ ['singkatan'] }})</span> @endif
          </td>
          <td style="text-align:center;">{{ ['program_total'] }}</td>
          <td style="text-align:center;">{{ ['program_selesai'] }}</td>
          <td style="text-align:center;">{{  }}%</td>
          <td style="text-align:right;">{{ ['rab'] > 0 ? number_format(['rab'],0,',','.') : '-' }}</td>
          <td style="text-align:right;">{{ ['realisasi'] > 0 ? number_format(['realisasi'],0,',','.') : '-' }}</td>
          <td style="text-align:center;">{{ ['peserta'] > 0 ? number_format(['peserta'],0,',','.') : '-' }}</td>
        </tr>
        @endif
      @empty
        <tr><td colspan="8" style="text-align:center;color:#94a3b8;padding:20px;">Tidak ada data bidang.</td></tr>
      @endforelse
    </tbody>
  </table>

  <div class="footer">Sistem Informasi Kabupaten Bekasi Hebat &bull; kabupatenbekasihebat.com</div>
</body>
</html>
