<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'transactions';

    // Deshabilita las marcas de tiempo automáticas si no se necesitan
    public $timestamps = false;

    // Atributos asignables en masa
    protected $fillable = [
        'client_id',
        'transaction_type',
        'amount',
        'created_at',
    ];

    // Los tipos de datos de los atributos para cast automático
    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    // Relaciones
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id')->onDelete('cascade');
    }
}
