<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PencatatanAir extends Model
{
    use HasFactory;

    protected $table = 'pencatatan_air';

    protected $fillable = [
        'master_penghuni_id',
        'periode_bulan',
        'periode_tahun',
        'meter_lalu',
        'meter_kini',
        'total_tagihan',
    ];

    protected $casts = [
        'master_penghuni_id' => 'integer',
        'periode_bulan' => 'integer',
        'periode_tahun' => 'integer',
        'meter_lalu' => 'double',
        'meter_kini' => 'double',
        'total_tagihan' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function masterPenghuni(): BelongsTo
    {
        return $this->belongsTo(MasterPenghuni::class, 'master_penghuni_id');
    }

    public function getPemakaianAttribute(): float
    {
        return $this->meter_kini - $this->meter_lalu;
    }

    public function getPeriodeLabelAttribute(): string
    {
        return sprintf('%02d-%04d', $this->periode_bulan, $this->periode_tahun);
    }

    public function getTotalTagihanAirAttribute(): float
    {
        // Ambil harga per kubik
        $hargakubik = MasterConfig::where('code', 'harga-air')->first();
        $hargaabodemen = MasterConfig::where('code', 'abodemen-air')->first();

        return ($this->meter_kini - $this->meter_lalu) * $hargakubik->value + $hargaabodemen->value;
    }

}
