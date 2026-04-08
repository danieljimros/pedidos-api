<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = [
        'cliente',
        'producto',
        'cantidad',
        'precio',
        'estado',
    ];

}
