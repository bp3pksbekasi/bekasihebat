<div>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:16px;">
        <div>
            <h1 style="font-size:24px;font-weight:700;color:#0f172a;margin:0;">Laporan Kinerja DPD</h1>
            <p style="color:#64748b;font-size:14px;margin:4px 0 0 0;">Rekapitulasi program kerja dan serapan anggaran tingkat DPD.</p>
        </div>
        <div style="display:flex;gap:12px;align-items:center;">
            <select wire:model.live="year" style="height:38px;padding:0 12px;border-radius:8px;border:1px solid #cbd5e1;background:white;font-size:14px;color:#1e293b;outline:none;">
                @foreach (range(date('Y') - 2, date('Y') + 1) as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
            <select wire:model.live="month" style="height:38px;padding:0 12px;border-radius:8px;border:1px solid #cbd5e1;background:white;font-size:14px;color:#1e293b;outline:none;">
                <option value="">Semua Bulan</option>
                @foreach (range(1, 12) as $m)
                    <option value="{{ sprintf('%02d', $m) }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                @endforeach
            </select>
            <a href="{{ route('reports.kinerja-dpd.print', ['year' => $year, 'month' => $month]) }}" target="_blank" style="display:inline-flex;align-items:center;gap:8px;height:38px;padding:0 16px;border-radius:8px;background:#fe5000;color:white;text-decoration:none;font-size:14px;font-weight:600;transition:all 0.2s;">
                <i class="ti ti-printer"></i> Cetak PDF
            </a>
        </div>
    </div>

    {{-- Top Metrics --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(240px, 1fr));gap:16px;margin-bottom:24px;">
        <div style="background:white;border-radius:12px;padding:20px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
            <div style="font-size:13px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Penyelesaian Program</div>
            <div style="margin-top:12px;display:flex;align-items:baseline;gap:8px;">
                <span style="font-size:28px;font-weight:700;color:#0f172a;">{{ $metrics['persen_selesai'] }}%</span>
                <span style="font-size:14px;color:#64748b;">({{ $metrics['total_selesai'] }} dari {{ $metrics['total_program'] }})</span>
            </div>
            <div style="width:100%;height:6px;background:#f1f5f9;border-radius:99px;margin-top:16px;overflow:hidden;">
                <div style="height:100%;background:#10b981;border-radius:99px;width:{{ $metrics['persen_selesai'] }}%;"></div>
            </div>
        </div>

        <div style="background:white;border-radius:12px;padding:20px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
            <div style="font-size:13px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Serapan Anggaran</div>
            <div style="margin-top:12px;display:flex;align-items:baseline;gap:8px;">
                <span style="font-size:28px;font-weight:700;color:#0f172a;">{{ $metrics['persen_serapan'] }}%</span>
            </div>
            <div style="margin-top:8px;font-size:13px;color:#64748b;">
                <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                    <span>RAB:</span> <span style="color:#0f172a;font-weight:500;">Rp{{ number_format($metrics['total_rab'], 0, ',', '.') }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;">
                    <span>Realisasi:</span> <span style="color:#0f172a;font-weight:500;">Rp{{ number_format($metrics['total_realisasi'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div style="background:white;border-radius:12px;padding:20px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.05);display:flex;flex-direction:column;justify-content:center;">
            <div style="font-size:13px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Keterlibatan Peserta</div>
            <div style="margin-top:12px;display:flex;align-items:center;gap:12px;">
                <div style="width:48px;height:48px;border-radius:12px;background:#fff7ed;color:#ea580c;display:flex;align-items:center;justify-content:center;font-size:24px;">
                    <i class="ti ti-users"></i>
                </div>
                <div>
                    <div style="font-size:28px;font-weight:700;color:#0f172a;">{{ number_format($metrics['total_peserta'], 0, ',', '.') }}</div>
                    <div style="font-size:13px;color:#64748b;">Orang Terlibat</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div style="background:white;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.05);margin-bottom:24px;padding:24px;">
        <h2 style="font-size:16px;font-weight:700;color:#0f172a;margin:0 0 20px 0;">Distribusi Program per Bidang</h2>
        <div style="height:300px;width:100%;position:relative;">
            <canvas id="bidangChart"></canvas>
        </div>
    </div>

    {{-- Table --}}
    <div style="background:white;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.05);overflow:hidden;">
        <div style="padding:20px 24px;border-bottom:1px solid #e2e8f0;background:#f8fafc;">
            <h2 style="font-size:16px;font-weight:700;color:#0f172a;margin:0;">Rincian Kinerja per Bidang</h2>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;text-align:left;">
                <thead>
                    <tr style="border-bottom:1px solid #e2e8f0;background:#f8fafc;">
                        <th style="padding:14px 24px;font-size:13px;font-weight:600;color:#64748b;text-transform:uppercase;">Bidang</th>
                        <th style="padding:14px 24px;font-size:13px;font-weight:600;color:#64748b;text-transform:uppercase;text-align:center;">Program Selesai</th>
                        <th style="padding:14px 24px;font-size:13px;font-weight:600;color:#64748b;text-transform:uppercase;text-align:right;">RAB (Rp)</th>
                        <th style="padding:14px 24px;font-size:13px;font-weight:600;color:#64748b;text-transform:uppercase;text-align:right;">Realisasi (Rp)</th>
                        <th style="padding:14px 24px;font-size:13px;font-weight:600;color:#64748b;text-transform:uppercase;text-align:center;">Serapan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekapPerBidang as $rekap)
                        @if($rekap['program_total'] > 0)
                            @php
                                $persen = $rekap['rab'] > 0 ? round(($rekap['realisasi'] / $rekap['rab']) * 100, 1) : 0;
                            @endphp
                            <tr style="border-bottom:1px solid #f1f5f9;transition:background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                                <td style="padding:16px 24px;">
                                    <div style="display:flex;align-items:center;gap:12px;">
                                        <div style="width:36px;height:36px;border-radius:8px;background:{{ $rekap['color'] }}15;color:{{ $rekap['color'] }};display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;">
                                            {{ $rekap['singkatan'] }}
                                        </div>
                                        <div>
                                            <div style="font-weight:600;color:#0f172a;font-size:14px;">{{ $rekap['nama'] }}</div>
                                            <div style="font-size:12px;color:#64748b;">{{ $rekap['peserta'] }} peserta dilibatkan</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding:16px 24px;text-align:center;">
                                    <span style="font-weight:600;color:#0f172a;">{{ $rekap['program_selesai'] }}</span>
                                    <span style="color:#64748b;font-size:13px;">/ {{ $rekap['program_total'] }}</span>
                                </td>
                                <td style="padding:16px 24px;text-align:right;font-size:14px;color:#475569;">
                                    {{ number_format($rekap['rab'], 0, ',', '.') }}
                                </td>
                                <td style="padding:16px 24px;text-align:right;font-size:14px;font-weight:500;color:#0f172a;">
                                    {{ number_format($rekap['realisasi'], 0, ',', '.') }}
                                </td>
                                <td style="padding:16px 24px;text-align:center;">
                                    <div style="display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:99px;background:{{ $persen >= 80 ? '#dcfce7' : ($persen >= 50 ? '#fef9c3' : '#fee2e2') }};color:{{ $persen >= 80 ? '#166534' : ($persen >= 50 ? '#854d0e' : '#991b1b') }};font-size:12px;font-weight:600;">
                                        {{ $persen }}%
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="5" style="padding:32px;text-align:center;color:#64748b;">Tidak ada data kegiatan di periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Chart.js Initialization --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js" data-navigate-once></script>
    <script data-navigate-track>
        document.addEventListener('livewire:navigated', () => {
            let chartInstance = null;

            const renderChart = () => {
                const ctx = document.getElementById('bidangChart');
                if (!ctx) return;
                
                // Parse PHP array to JS
                const rawData = @json($rekapPerBidang);
                const labels = [];
                const dataTotal = [];
                const dataSelesai = [];
                const colors = [];

                for (const key in rawData) {
                    if (rawData[key].program_total > 0) {
                        labels.push(rawData[key].singkatan);
                        dataTotal.push(rawData[key].program_total);
                        dataSelesai.push(rawData[key].program_selesai);
                        colors.push(rawData[key].color || '#fe5000');
                    }
                }

                if (chartInstance) {
                    chartInstance.destroy();
                }

                chartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Program Selesai',
                                data: dataSelesai,
                                backgroundColor: colors,
                                borderRadius: 4,
                                borderSkipped: false,
                            },
                            {
                                label: 'Total Program',
                                data: dataTotal,
                                backgroundColor: '#e2e8f0',
                                borderRadius: 4,
                                borderSkipped: false,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                },
                                grid: {
                                    color: '#f1f5f9'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            };

            renderChart();

            // Re-render chart when livewire updates
            Livewire.hook('morph.updated', ({ component, el }) => {
                if (component.name === 'reports.kinerja-dpd' || document.getElementById('bidangChart')) {
                    renderChart();
                }
            });
        });
    </script>
    @endpush
</div>
