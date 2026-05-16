<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penyewa extends Model
{
    use HasFactory;

    protected $fillable = [
        'master_penghuni_id',
        'nama_penyewa',
        'tgl_mulai_sewa',
        'tgl_selesai_sewa',
        'jml_anggota',
        'status',
    ];

    public function masterPenghuni()
    {
        return $this->belongsTo(MasterPenghuni::class);
    }
}
