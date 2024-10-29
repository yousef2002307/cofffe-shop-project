<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\OrderDetail;
use App\Models\Cart;
class Product extends Model
{
 use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'image',
    ];
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
    public function cart()
    {
        return $this->hasMany(Cart::class);
    }
}
