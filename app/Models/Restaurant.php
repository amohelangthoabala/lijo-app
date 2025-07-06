<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'domain', 'logo'];

    protected $appends = ['logo_url'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
   
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Product::class);
    }

    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    // public function scopeActive($query)
    // {
    //     return $query->where('status', 'active');
    // }
}
