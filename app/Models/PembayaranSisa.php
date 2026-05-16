<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranSisa extends Model
{
    protected $table = 'pembayaran_sisa';

    protected $fillable = [
        'master_penghuni_id',
        'sisa_lebih_bayar',
        'periode',
    ];

    public function masterPenghuni()
    {
        return $this->belongsTo(MasterPenghuni::class);
    }
}
