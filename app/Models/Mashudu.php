<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mashudu extends Model
{
    use HasFactory;

    
    protected $table = 'mashudu';

    protected $primaryKey = 'mashudu_id';

    
    protected $fillable = [
        'tarehe',
        'mashudu',
        'price',
        'discount',
        'alizeti_id',
        'batch_no',
        'user_id',
        'total_price',
        'prices_id',
        'price_id',
        'payment_way',
    ];

    
    public function alizeti()
    {
        return $this->belongsTo(Alizeti::class, 'alizeti_id', 'alizeti_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function price()
    {
        return $this->belongsTo(Price::class, 'price_id', 'prices_id');
    }
}
