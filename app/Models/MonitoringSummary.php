<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringSummary extends Model
{
    protected $table = 'monitoring_summary';
    
    protected $fillable = [
        'id_aset', 'tanggal', 'group_aset', 'area',
        'total_waktu_kerja', 'total_waktu_operasi', 'total_waktu_idle',
        'rata_idle', 'total_bahan_bakar', 'rata_bahan_bakar'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function getBulanAttribute()
    {
        return $this->tanggal->format('F');
    }

    public function getTahunAttribute()
    {
        return $this->tanggal->year;
    }
}