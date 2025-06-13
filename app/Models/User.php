<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'role_id', // Include role_id as a mass-assignable attribute
    ];

    public function role()
    {
        return $this->belongsTo(Role::class); // Define the relationship to the Role model
    }
    public function alizetiRecords()
    {
        return $this->hasMany(Alizeti::class, 'user_id');
    }

    public function prices()
    {
        return $this->hasMany(Price::class, 'user_id', 'id');
    }
}
