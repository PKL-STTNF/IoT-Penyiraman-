<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Irrigation;
use Carbon\Carbon;

class IrrigationController extends Controller
{
    public function index()
    {
        $data = Irrigation::latest()->first();

        if (!$data) {
            $data = (object) [
                'suhu_udara'            => 0,
                'kelembapan_udara'      => 0,
                'kelembapan_tanah'      => 0,
                'suhu_tanah'            => 0,
                'mode'                  => 'otomatis',
                'status_pompa'          => 'OFF',
                'pompa_dinyalakan_pada' => null,
                'durasi_penyiraman'     => 5,
                'jadwal_pagi'           => '06:00',
                'jadwal_sore'           => '16:30',
            ];
        }

        // Cek jika pompa sedang ON, apakah durasi penyiraman sudah habis?
        if ($data instanceof Irrigation && $data->status_pompa === 'ON' && $data->pompa_dinyalakan_pada) {
            $waktuSelesai = Carbon::parse($data->pompa_dinyalakan_pada)->addMinutes($data->durasi_penyiraman);
            if (Carbon::now()->greaterThanOrEqualTo($waktuSelesai)) {
                $data->update([
                    'status_pompa' => 'OFF',
                    'pompa_dinyalakan_pada' => null
                ]);
            }
        }

        return view('dashboard', compact('data'));
    }

    public function togglePompa()
    {
        $data = Irrigation::latest()->first();
        $statusBaru = ($data && $data->status_pompa === 'ON') ? 'OFF' : 'ON';
        $waktuMulai = ($statusBaru === 'ON') ? Carbon::now() : null;

        if ($data) {
            $data->update([
                'status_pompa'          => $statusBaru,
                'pompa_dinyalakan_pada' => $waktuMulai,
            ]);
        } else {
            Irrigation::create([
                'suhu_udara'            => 0,
                'kelembapan_udara'      => 0,
                'kelembapan_tanah'      => 0,
                'suhu_tanah'            => 0,
                'mode'                  => 'otomatis',
                'status_pompa'          => $statusBaru,
                'pompa_dinyalakan_pada' => $waktuMulai,
                'durasi_penyiraman'     => 5,
                'jadwal_pagi'           => '06:00:00',
                'jadwal_sore'           => '16:30:00',
            ]);
        }

        return redirect()->back()->with('success', 'Status pompa berhasil diperbarui!');
    }

    public function updateMode(Request $request)
    {
        $data = Irrigation::latest()->first();

        if ($data) {
            $data->update(['mode' => $request->input('mode', 'otomatis')]);
        } else {
            Irrigation::create([
                'suhu_udara'        => 0,
                'kelembapan_udara'  => 0,
                'kelembapan_tanah'  => 0,
                'suhu_tanah'        => 0,
                'mode'              => $request->input('mode', 'otomatis'),
                'status_pompa'      => 'OFF',
                'durasi_penyiraman' => 5,
                'jadwal_pagi'       => '06:00:00',
                'jadwal_sore'       => '16:30:00',
            ]);
        }

        return redirect()->back();
    }

    public function updateJadwal(Request $request)
    {
        $request->validate([
            'jadwal_pagi'        => 'required',
            'jadwal_sore'        => 'required',
            'durasi_penyiraman' => 'required|numeric|min:1|max:60',
        ]);

        $data = Irrigation::latest()->first();

        if ($data) {
            $data->update([
                'jadwal_pagi'        => $request->jadwal_pagi,
                'jadwal_sore'        => $request->jadwal_sore,
                'durasi_penyiraman' => $request->durasi_penyiraman,
            ]);
        } else {
            Irrigation::create([
                'suhu_udara'        => 0,
                'kelembapan_udara'  => 0,
                'kelembapan_tanah'  => 0,
                'suhu_tanah'        => 0,
                'mode'              => 'otomatis',
                'status_pompa'      => 'OFF',
                'durasi_penyiraman' => $request->durasi_penyiraman,
                'jadwal_pagi'       => $request->jadwal_pagi,
                'jadwal_sore'       => $request->jadwal_sore,
            ]);
        }

        return redirect()->back()->with('success', 'Jadwal dan durasi penyiraman berhasil disimpan!');
    }

    /**
     * API Endpoint untuk dibaca Alat IoT (ESP8266/ESP32)
     */
    public function getApiData()
    {
        $data = Irrigation::latest()->first();

        if (!$data) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data penyiraman belum diset'
            ], 404);
        }

        return response()->json([
            'status'            => 'success',
            'mode'              => $data->mode,
            'status_pompa'      => $data->status_pompa,
            'jadwal_pagi'       => $data->jadwal_pagi,
            'jadwal_sore'       => $data->jadwal_sore,
            'durasi_penyiraman' => (int) $data->durasi_penyiraman,
        ], 200);
    }
}