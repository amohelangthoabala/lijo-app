<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'phone', 'email', 'delivery_address', 'status', 'total'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
