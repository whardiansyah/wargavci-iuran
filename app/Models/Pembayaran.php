<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $fillable = [
        'master_penghuni_id',
        'periode',
        'jumlah_tagihan',
        'jumlah_bayar',
        'sisa_lebih_bayar',
        'tanggal_bayar',
        'cara_bayar',
    ];

    protected $casts = [
        'master_penghuni_id' => 'integer',
        'jumlah_tagihan' => 'integer',
        'jumlah_bayar' => 'integer',
        'sisa_lebih_bayar' => 'integer',
        'tanggal_bayar' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function masterPenghuni(): BelongsTo
    {
        return $this->belongsTo(MasterPenghuni::class, 'master_penghuni_id');
    }
}
