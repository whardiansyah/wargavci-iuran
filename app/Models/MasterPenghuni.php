<?php

namespace App\Models;

use App\Models\PencatatanAir;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterPenghuni extends Model
{
    use HasFactory;

    protected $table = 'master_penghuni';

    protected $fillable = [
        'kepala_keluarga',
        'kontak_person',
        'nomor_rumah',
        'status_rumah',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get status label for display
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status_rumah) {
            'pribadi' => 'Pribadi',
            'sewa' => 'Sewa',
            'kosong' => 'Kosong',
            default => 'Unknown',
        };
    }

    /**
     * Get status color for badge
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status_rumah) {
            'pribadi' => 'blue',
            'sewa' => 'green',
            'kosong' => 'gray',
            default => 'gray',
        };
    }

    public function getStatusAktifLabelAttribute(): string
    {
        return match ($this->status) {
            'aktif' => 'Aktif',
            'tidak aktif' => 'Tidak Aktif',
            default => 'Aktif',
        };
    }

    public function getStatusAktifColorAttribute(): string
    {
        return match ($this->status) {
            'aktif' => 'success',
            'tidak aktif' => 'secondary',
            default => 'success',
        };
    }

    public function pencatatanAir(): HasMany
    {
        return $this->hasMany(PencatatanAir::class, 'master_penghuni_id');
    }

    public function tagihan(): HasMany
    {
        return $this->hasMany(Tagihan::class, 'master_penghuni_id');
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'master_penghuni_id');
    }
}
