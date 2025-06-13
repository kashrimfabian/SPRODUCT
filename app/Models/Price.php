<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;
    protected $primaryKey = 'prices_id';

    protected $fillable = [
        'price_per_litre',
        'price_per_20_litre',
        'price_of_mashudu',
        'alizeti_id',
        'user_id',
        'price_of_lami', 
        'price_of_ugido', 
    ];

    public function mashudu()
    {
        return $this->hasMany(Mashudu::class, 'prices_id', 'prices_id');
    }

    
    public function alizeti()
    {
        return $this->belongsTo(Alizeti::class, 'alizeti_id','ali_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function mauzo()
    {
        return $this->hasMany(Mauzo::class, 'prices_id', 'prices_id');
    }

}
