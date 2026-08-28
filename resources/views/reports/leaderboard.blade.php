<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center py-8">

    <div class="mb-6 flex gap-4">
        <a href="{{ route('buku-induk-rw.index') }}" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-bold shadow transition-colors">
            &larr; Kembali
        </a>
        <button onclick="downloadImage()" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow-lg transition-transform transform hover:scale-105 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Download Gambar (PNG)
        </button>
    </div>

    <div class="text-center mb-4">
        <p class="text-sm text-gray-500 mb-1">Gunakan resolusi 1080x1080 (Format Instagram Square)</p>
    </div>

    <!-- Container for Image -->
    <div id="capture-area" class="shadow-2xl overflow-hidden" style="width: 1080px; height: 1080px;">
        <iframe src="{{ route('buku-induk-rw.leaderboard-image', request()->all()) }}" frameborder="0" width="1080" height="1080" scrolling="no" id="content-frame"></iframe>
    </div>

    <script>
        function downloadImage() {
            const btn = document.querySelector('button');
            const originalText = btn.innerHTML;
            btn.innerHTML = 'Memproses...';
            btn.disabled = true;

            const frame = document.getElementById('content-frame');
            const frameDoc = frame.contentDocument || frame.contentWindow.document;

            html2canvas(frameDoc.body, {
                scale: 2, // High quality
                useCORS: true,
                allowTaint: true,
                backgroundColor: '#0f172a'
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = `Leaderboard_Bekasi_Hebat_{{ date('Ymd') }}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();

                btn.innerHTML = originalText;
                btn.disabled = false;
            }).catch(err => {
                alert('Terjadi kesalahan saat memproses gambar.');
                console.error(err);
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }
    </script>
</body>
</html>
