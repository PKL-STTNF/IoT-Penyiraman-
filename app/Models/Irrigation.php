<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Irrigation extends Model
{
    use HasFactory;

    protected $fillable = [
        'suhu_udara',
        'kelembapan_udara',
        'kelembapan_tanah',
        'suhu_tanah',
        'mode',
        'status_pompa',
        'pompa_dinyalakan_pada',
        'durasi_penyiraman',
        'jadwal_pagi',
        'jadwal_sore',
    ];
}