<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $primaryKey = 'payment_id'; 

    protected $fillable = ['name', 'description', 'is_active'];

    public function mauzo()
    {
        return $this->hasMany(Mauzo::class, 'payment_id');
    }
}
