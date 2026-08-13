<!DOCTYPE html>
<html lang="id" style="height:100%;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Pemetaan Strategi Pilkades' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
        }
        body > div {
            height: 100%;
        }
        .sheet-scroll {
            overflow: auto;
            scroll-behavior: smooth;
        }
        .sheet-scroll::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }
        .sheet-scroll::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .sheet-scroll::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 5px;
        }
        .sheet-scroll::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }
        .sheet-scroll::-webkit-scrollbar-corner {
            background: #f1f5f9;
        }
    </style>
</head>
<body>
    {{ $slot }}
    @livewireScripts
</body>
</html>

