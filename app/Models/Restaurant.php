<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'domain', 'logo'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
   
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
