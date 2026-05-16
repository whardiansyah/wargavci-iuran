<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiKas extends Model
{
    use HasFactory;

    protected $table = 'transaksi_kas';

    protected $fillable = [
        'tanggal',
        'kode',
        'deskripsi',
        'keterangan',
        'kredit',
        'debet',
        'nomor_ref',
        'saldo',
        'jenis',
        'periode_bulan',
        'periode_tahun',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'kredit' => 'integer',
        'debet' => 'integer',
        'saldo' => 'integer',
        'periode_bulan' => 'integer',
        'periode_tahun' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
