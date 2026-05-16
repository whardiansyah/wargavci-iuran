<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterConfig extends Model
{
    use HasFactory;

    protected $table = 'master_configs';

    protected $fillable = [
        'value',
        'deskripsi',
        'type',
        'code',
    ];
}
