<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $primaryKey = 'stock_id';

    protected $fillable = [
        'alizeti_id',
        'mafuta_masafi',
        'mashudu',
        'mafuta_machafu',
        'total_al_kgms',
        'ugido',
        'lami',
    ];

    public function alizeti()
    {
        return $this->belongsTo(Alizeti::class, 'alizeti_id');
    }

    
}