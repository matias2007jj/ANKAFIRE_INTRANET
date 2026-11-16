<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    // Agregamos la propiedad $fillable para permitir la asignación masiva
    protected $fillable = [
        'codigo_cliente',
        'razon_social',
        'nombre_comercial',
        'ruc',
        'direccion',
        'provincia',
        'contacto',
        'cargo',
        'correo',
        'telefono',
        'estado',
    ];

    public function equipos()
    {
        return $this->hasMany(Equipo::class, 'codigo_cliente', 'codigo_cliente');
    }
}