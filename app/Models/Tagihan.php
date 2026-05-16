<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tagihan extends Model
{
    use HasFactory;

    protected $table = 'tagihan';

    protected $fillable = [
        'master_penghuni_id',
        'periode',
        'code',
        'nilai',
        'status_bayar',
    ];

    protected $casts = [
        'master_penghuni_id' => 'integer',
        'nilai' => 'double',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function masterPenghuni(): BelongsTo
    {
        return $this->belongsTo(MasterPenghuni::class, 'master_penghuni_id');
    }
}
