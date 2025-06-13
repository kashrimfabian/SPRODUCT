<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name']; // Define fillable attributes

    public function users()
    {
        return $this->hasMany(User::class); // Define the relationship to the User model
    }
}
