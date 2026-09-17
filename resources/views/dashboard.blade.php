<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IoT-Penyiraman</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Auto Refresh tiap 3 menit -->
    <meta http-equiv="refresh" content="180">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .white-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px -2px rgba(33, 20, 20, 0.05);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen relative overflow-x-hidden pb-12">

    <!-- Navbar -->
    <header class="bg-white border-b border-slate-200 px-6 py-4 mb-8 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-wider">IoT-Penyiraman-</h1>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 space-y-8">

        <!-- Flash Message -->
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-between text-sm font-medium shadow-sm">
                <div class="flex items-center gap-2">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- SECTION 1: KONTROL SAKELAR & MODE -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Card Mode Kontrol -->
            <div class="white-card rounded-2xl p-6">
                <div class="mb-4">
                    <h3 class="text-base font-bold text-slate-900">Mode Pengoperasian</h3>
                    <p class="text-xs text-slate-500">Pilih sistem kontrol otomatis atau manual</p>
                </div>

                <form action="{{ route('update.mode') }}" method="POST" class="grid grid-cols-2 gap-3 mt-4">
                    @csrf
                    <button type="submit" name="mode" value="otomatis" 
                        class="p-4 rounded-xl font-bold text-xs flex flex-col items-center justify-center gap-2 transition-all duration-200 border {{ $data->mode === 'otomatis' ? 'bg-emerald-600 text-white border-emerald-600 shadow-md shadow-emerald-200' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                        <span>OTOMATIS (SENSOR)</span>
                    </button>
                    
                    <button type="submit" name="mode" value="manual" 
                        class="p-4 rounded-xl font-bold text-xs flex flex-col items-center justify-center gap-2 transition-all duration-200 border {{ $data->mode === 'manual' ? 'bg-emerald-600 text-white border-emerald-600 shadow-md shadow-emerald-200' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                        <span>MANUAL (USER)</span>
                    </button>
                </form>
            </div>

            <!-- Card Control Sakelar Pompa -->
            <div class="white-card rounded-2xl p-6">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Status Pompa Air</h3>
                        <p class="text-xs text-slate-500">Durasi siram: {{ $data->durasi_penyiraman ?? 5 }} Menit</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-black tracking-wider border {{ $data->status_pompa === 'ON' ? 'bg-emerald-100 text-emerald-700 border-emerald-200 animate-pulse' : 'bg-rose-100 text-rose-700 border-rose-200' }}">
                        {{ $data->status_pompa }}
                    </span>
                </div>

                <!-- TIMER COUNTDOWN JIKA POMPA NYALA -->
                @if($data->status_pompa === 'ON' && $data->pompa_dinyalakan_pada)
                    @php
                        $waktuMulai = \Carbon\Carbon::parse($data->pompa_dinyalakan_pada);
                        $waktuSelesai = $waktuMulai->copy()->addMinutes($data->durasi_penyiraman ?? 5);
                        $sisaDetik = max(0, \Carbon\Carbon::now()->diffInSeconds($waktuSelesai, false));
                    @endphp
                    <div class="mt-3 p-3 bg-emerald-50 rounded-xl border border-emerald-200 flex items-center justify-between">
                        <div class="text-emerald-800 text-xs font-semibold">
                            <span>Sedang Menyiram Tanaman...</span>
                        </div>
                        <div class="text-sm font-black text-emerald-700 tracking-wider font-mono">
                            Sisa Waktu: <span id="countdown-timer">--:--</span>
                        </div>
                    </div>

                    <script>
                        let totalSeconds = Math.floor({{ $sisaDetik }});
                        const timerElement = document.getElementById('countdown-timer');

                        function updateTimer() {
                            if (totalSeconds <= 0) {
                                timerElement.innerText = "Selesai!";
                                setTimeout(() => { window.location.reload(); }, 1000);
                                return;
                            }
                            let minutes = Math.floor(totalSeconds / 60);
                            let seconds = Math.floor(totalSeconds % 60);
                            timerElement.innerText = 
                                (minutes < 10 ? '0' : '') + minutes + ':' + 
                                (seconds < 10 ? '0' : '') + seconds;
                            totalSeconds--;
                        }
                        updateTimer();
                        setInterval(updateTimer, 1000);
                    </script>
                @endif

                <div class="mt-4 flex items-center justify-between gap-4 bg-slate-50 p-3 rounded-xl border border-slate-200">
                    <div class="text-xs text-slate-500">
                        @if($data->mode === 'otomatis')
                            <span class="text-amber-600 font-semibold">
                                Mode Otomatis Aktif
                            </span>
                        @else
                            <span class="text-slate-600">Kontrol sakelar bebas digunakan.</span>
                        @endif
                    </div>

                    <form action="{{ route('toggle.pompa') }}" method="POST">
                        @csrf
                        <button type="submit" 
                            class="px-6 py-3 rounded-xl font-extrabold text-xs tracking-wider transition-all duration-200 flex items-center gap-2 shadow-md border {{ $data->status_pompa === 'ON' ? 'bg-rose-600 hover:bg-rose-700 text-white border-rose-600 shadow-rose-200' : 'bg-emerald-600 hover:bg-emerald-700 text-white border-emerald-600 shadow-emerald-200' }}">
                            <i data-lucide="power" class="w-4 h-4"></i>
                            {{ $data->status_pompa === 'ON' ? 'MATIKAN POMPA' : 'NYALAKAN POMPA' }}
                        </button>
                    </form>
                </div>
            </div>

        </div>

        <!-- SECTION 2: MONITORING SENSOR REALTIME (4 PARAMETER) -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-slate-900">
                    Monitoring Telemetri Sensor
                </h2>
                <span class="text-xs text-slate-500">Auto-update tiap 3m</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

                <!-- 1. Suhu Tanah -->
                <div class="white-card rounded-2xl p-5 hover:border-orange-400 transition duration-200">
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-xs font-bold text-slate-400 tracking-wider">SUHU TANAH</span>
                        <div class="p-2 bg-orange-50 text-orange-600 rounded-lg border border-orange-200">
                            <i data-lucide="thermometer" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-black text-slate-900 tracking-tight">{{ empty($data->suhu_tanah) || $data->suhu_tanah == 0 ? '--' : $data->suhu_tanah }}</span>
                        <span class="text-lg font-bold text-slate-400">°C</span>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>DS18B20</span>
                        <span class="text-orange-700 font-semibold bg-orange-50 px-2 py-0.5 rounded border border-orange-200">Tanah</span>
                    </div>
                </div>

                <!-- 2. Suhu Udara -->
                <div class="white-card rounded-2xl p-5 hover:border-amber-400 transition duration-200">
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-xs font-bold text-slate-400 tracking-wider">SUHU UDARA</span>
                        <div class="p-2 bg-amber-50 text-amber-600 rounded-lg border border-amber-200">
                            <i data-lucide="sun" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-black text-slate-900 tracking-tight">{{ empty($data->suhu_udara) || $data->suhu_udara == 0 ? '--' : $data->suhu_udara }}</span>
                        <span class="text-lg font-bold text-slate-400">°C</span>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>DHT11/DHT22</span>
                        <span class="text-amber-700 font-semibold bg-amber-50 px-2 py-0.5 rounded border border-amber-200">Udara</span>
                    </div>
                </div>

                <!-- 3. Kelembapan Tanah -->
                <div class="white-card rounded-2xl p-5 hover:border-emerald-400 transition duration-200">
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-xs font-bold text-slate-400 tracking-wider">KELEMBAPAN TANAH</span>
                        <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg border border-emerald-200">
                            <i data-lucide="sprout" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-black text-slate-900 tracking-tight">{{ empty($data->kelembapan_tanah) || $data->kelembapan_tanah == 0 ? '--' : $data->kelembapan_tanah }}</span>
                        <span class="text-lg font-bold text-slate-400">%</span>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>Soil Moisture</span>
                        <span class="font-semibold px-2 py-0.5 rounded border {{ $data->kelembapan_tanah < 50 ? 'text-rose-700 bg-rose-50 border-rose-200' : 'text-emerald-700 bg-emerald-50 border-emerald-200' }}">
                            {{ $data->kelembapan_tanah < 50 ? 'Kering' : 'Lembap' }}
                        </span>
                    </div>
                </div>

                <!-- 4. Kelembapan Udara -->
                <div class="white-card rounded-2xl p-5 hover:border-blue-400 transition duration-200">
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-xs font-bold text-slate-400 tracking-wider">KELEMBAPAN UDARA</span>
                        <div class="p-2 bg-blue-50 text-blue-600 rounded-lg border border-blue-200">
                            <i data-lucide="droplets" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-black text-slate-900 tracking-tight">{{ empty($data->kelembapan_udara) || $data->kelembapan_udara == 0 ? '--' : $data->kelembapan_udara }}</span>
                        <span class="text-lg font-bold text-slate-400">%</span>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>DHT11/DHT22</span>
                        <span class="text-blue-700 font-semibold bg-blue-50 px-2 py-0.5 rounded border border-blue-200">Udara</span>
                    </div>
                </div>

            </div>
        </div>

        <!-- SECTION 3: PENGATURAN JADWAL DAN DURASI PENYIRAMAN -->
        <div class="white-card rounded-2xl p-6">
            <div class="mb-6">
                <h3 class="text-base font-bold text-slate-900">Pengaturan Jam & Durasi Penyiraman</h3>
                <p class="text-xs text-slate-500">Atur jam rutin dan lama waktu pompa menyiram sekali jalan</p>
            </div>

            <form action="{{ route('update.jadwal') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <!-- Jam Pagi -->
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-2">
                        <label class="text-xs font-bold text-slate-700 block">
                            Penyiraman Pagi
                        </label>
                        <input type="time" name="jadwal_pagi" 
                            value="{{ substr($data->jadwal_pagi, 0, 5) }}" 
                            class="w-full bg-white border border-slate-300 rounded-lg px-4 py-3 text-lg font-bold text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition">
                    </div>

                    <!-- Jam Sore -->
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-2">
                        <label class="text-xs font-bold text-slate-700 block">
                            Penyiraman Sore
                        </label>
                        <input type="time" name="jadwal_sore" 
                            value="{{ substr($data->jadwal_sore, 0, 5) }}" 
                            class="w-full bg-white border border-slate-300 rounded-lg px-4 py-3 text-lg font-bold text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition">
                    </div>

                    <!-- Durasi Siram -->
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-2">
                        <label class="text-xs font-bold text-slate-700 block">
                            Durasi Siram (Menit)
                        </label>
                        <input type="number" name="durasi_penyiraman" min="1" max="60"
                            value="{{ $data->durasi_penyiraman ?? 5 }}" 
                            class="w-full bg-white border border-slate-300 rounded-lg px-4 py-3 text-lg font-bold text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition">
                    </div>

                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                        class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md shadow-emerald-200 transition flex items-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        SIMPAN JADWAL & DURASI
                    </button>
                </div>
            </form>
        </div>

    </main>

    <!-- Lucide Icons -->
    <script>
        lucide.createIcons();
    </script>
</body>
</html>