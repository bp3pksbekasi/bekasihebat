<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard Pemenangan - {{ $periode }}</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&family=Montserrat:wght@700;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: url('https://kabupatenbekasihebat.com/images/bg-pattern.png') repeat, linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white;
            margin: 0;
            padding: 0;
            width: 1080px;
            height: 1080px;
            overflow: hidden;
            position: relative;
        }
        
        .title-font {
            font-family: 'Montserrat', sans-serif;
        }

        .gold-gradient {
            background: linear-gradient(to bottom, #FCE679, #D4AF37, #AA771C);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .card-glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }

        .rank-badge {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 20px;
            color: white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.5);
        }
        
        .rank-1 { background: linear-gradient(135deg, #FFD700, #D4AF37); border: 2px solid #FFF8DC; transform: scale(1.1); }
        .rank-2 { background: linear-gradient(135deg, #E2E2E2, #9E9E9E); border: 2px solid #F5F5F5; }
        .rank-3 { background: linear-gradient(135deg, #CD7F32, #8B4513); border: 2px solid #FFDAB9; }
        .rank-4, .rank-5 { background: #334155; border: 2px solid #475569; }

        .row-highlight-1 { background: linear-gradient(to right, rgba(212, 175, 55, 0.2), rgba(212, 175, 55, 0)); border-left: 4px solid #FFD700; }
        .row-highlight-2 { background: linear-gradient(to right, rgba(158, 158, 158, 0.2), rgba(158, 158, 158, 0)); border-left: 4px solid #E2E2E2; }
        .row-highlight-3 { background: linear-gradient(to right, rgba(205, 127, 50, 0.2), rgba(205, 127, 50, 0)); border-left: 4px solid #CD7F32; }

        .progress-bar-bg {
            background-color: rgba(255,255,255,0.1);
            border-radius: 999px;
            height: 12px;
            width: 100%;
            overflow: hidden;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .progress-bar-fill-orange {
            background: linear-gradient(90deg, #f97316, #ea580c);
            height: 100%;
            border-radius: 999px;
        }

        .progress-bar-fill-blue {
            background: linear-gradient(90deg, #3b82f6, #2563eb);
            height: 100%;
            border-radius: 999px;
        }

        /* Decorative elements */
        .decor-circle-1 { position: absolute; top: -100px; left: -100px; width: 400px; height: 400px; border-radius: 50%; background: radial-gradient(circle, rgba(234, 88, 12, 0.2) 0%, rgba(234, 88, 12, 0) 70%); z-index: 0; }
        .decor-circle-2 { position: absolute; bottom: -150px; right: -100px; width: 500px; height: 500px; border-radius: 50%; background: radial-gradient(circle, rgba(37, 99, 235, 0.2) 0%, rgba(37, 99, 235, 0) 70%); z-index: 0; }
    </style>
</head>
<body class="flex flex-col relative">

    <div class="decor-circle-1"></div>
    <div class="decor-circle-2"></div>

    <!-- Header Section -->
    <div class="relative z-10 text-center pt-12 pb-8 px-12 flex flex-col items-center">
        <!-- Logo Header -->
        <div class="flex items-center justify-center gap-4 mb-4">
            <img src="https://kabupatenbekasihebat.com/images/logo.png" alt="Logo" class="h-16" onerror="this.style.display='none'">
            <div class="h-12 w-px bg-white/20"></div>
            <h2 class="title-font text-2xl font-bold tracking-widest text-white/90">BEKASI HEBAT</h2>
        </div>
        
        <h1 class="title-font text-5xl font-black uppercase mb-3 gold-gradient">LEADERBOARD 5 BESAR</h1>
        
        <div class="inline-block bg-white/10 backdrop-blur-md border border-white/20 rounded-full px-8 py-2">
            <p class="text-xl font-bold text-white tracking-wide uppercase">{{ $periode }}</p>
        </div>
    </div>

    <!-- Main Content (2 Columns) -->
    <div class="relative z-10 flex-1 grid grid-cols-2 gap-8 px-12 pb-6">
        
        <!-- Kolom 1: Sisir RW -->
        <div class="card-glass flex flex-col p-6 h-[640px]">
            <div class="flex items-center gap-4 mb-6 pb-4 border-b border-white/10">
                <div class="bg-orange-500/20 p-3 rounded-xl border border-orange-500/30">
                    <svg class="w-8 h-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                </div>
                <div>
                    <h2 class="title-font text-2xl font-black text-white uppercase tracking-wider">Top 5 Sisir RW</h2>
                    <p class="text-orange-300 font-semibold text-sm">Berdasarkan Total Kegiatan Diinput</p>
                </div>
            </div>

            <div class="flex flex-col gap-4">
                @foreach($dataSisir as $index => $dpc)
                    @php $rank = $index + 1; @endphp
                    <div class="flex items-center bg-white/5 rounded-xl p-4 transition-transform {{ 'row-highlight-'.$rank }}">
                        <div class="rank-badge {{ 'rank-'.$rank }} mr-4 shrink-0 shadow-lg">{{ $rank }}</div>
                        <div class="flex-1">
                            <h3 class="font-bold text-lg text-white">DPC {{ strtoupper($dpc['kecamatan']) }}</h3>
                            <div class="text-orange-400 font-bold text-sm">TOTAL: {{ number_format($dpc['total_kegiatan']) }} GIAT</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Kolom 2: Infrastruktur -->
        <div class="card-glass flex flex-col p-6 h-[640px]">
            <div class="flex items-center gap-4 mb-6 pb-4 border-b border-white/10">
                <div class="bg-blue-500/20 p-3 rounded-xl border border-blue-500/30">
                    <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <h2 class="title-font text-2xl font-black text-white uppercase tracking-wider">Top 5 Infrastruktur</h2>
                    <p class="text-blue-300 font-semibold text-sm">Berdasarkan Persentase Keterisian</p>
                </div>
            </div>

            <div class="flex flex-col gap-4">
                @foreach($dataInfra as $index => $dpc)
                    @php 
                        $rank = $index + 1; 
                        $pct = $dpc['target_total'] > 0 ? round(($dpc['terisi_total'] / $dpc['target_total']) * 100, 1) : 0;
                    @endphp
                    <div class="flex items-center bg-white/5 rounded-xl p-4 transition-transform {{ 'row-highlight-'.$rank }}">
                        <div class="rank-badge {{ 'rank-'.$rank }} mr-4 shrink-0 shadow-lg">{{ $rank }}</div>
                        <div class="flex-1">
                            <div class="flex justify-between items-end mb-1">
                                <h3 class="font-bold text-lg text-white">DPC {{ strtoupper($dpc['kecamatan']) }}</h3>
                                <span class="text-blue-400 font-black text-lg">{{ $pct }}%</span>
                            </div>
                            <div class="progress-bar-bg mb-1">
                                <div class="progress-bar-fill-blue" style="width: {{ min(100, $pct) }}%"></div>
                            </div>
                            <div class="text-gray-400 text-xs font-semibold">
                                Terisi: {{ number_format($dpc['terisi_total']) }} / Target: {{ number_format($dpc['target_total']) }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- Footer Motivasi Section -->
    <div class="relative z-10 px-12 pb-10">
        <div class="card-glass bg-gradient-to-r from-orange-500/20 via-white/5 to-blue-500/20 p-6 flex flex-col items-center justify-center text-center border-t border-white/20">
            <h3 class="title-font text-2xl font-black text-white uppercase mb-2 tracking-wide">
                🎉 SELAMAT KEPADA DPC DENGAN PENCAPAIAN TERTINGGI! 🎉
            </h3>
            <p class="text-gray-300 font-semibold text-lg max-w-4xl italic">
                "Kemenangan besar selalu dimulai dari kerja-kerja terstruktur di akar rumput. 
                Mari terus rapatkan barisan, kejar target infrastruktur, dan sapa warga melalui Sisir RW. 
                Bekasi Hebat, Menang Bermartabat!"
            </p>
        </div>
    </div>

</body>
</html>
