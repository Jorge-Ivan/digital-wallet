<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'clients';

    // Deshabilita las marcas de tiempo (timestamps) si no se necesitan
    public $timestamps = false;

    // Atributos asignables en masa
    protected $fillable = [
        'document',
        'names',
        'email',
        'cellphone',
        'wallet_balance',
    ];

    // Los tipos de datos de los atributos para cast automático
    protected $casts = [
        'wallet_balance' => 'decimal:2',
    ];
}
