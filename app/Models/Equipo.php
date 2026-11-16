<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    protected $fillable = [
        'codigo_cliente',
        'codigo_sede',
        'numero_serie',
        'numero_interno',
        'tipo_extintor',
        'capacidad_carga',
        'marca',
        'anio_fabricacion',
        'fecha_fabricacion',
        'proximo_mantenimiento',
        'fecha_ultimo_mantenimiento',
        'vencimiento_ph',
        'fecha_prueba_hidrostatica',
        'estado',
        'fecha_ultimo_servicio',
        'numero_certificado',
        'ruta_cert_operatividad',
        'ruta_informe_tecnico',
        'ruta_cert_ph',
    ];

    protected $casts = [
        'fecha_fabricacion' => 'date',
        'fecha_ultimo_mantenimiento' => 'date',
        'vencimiento_ph' => 'date',
        'fecha_prueba_hidrostatica' => 'date',
        'fecha_ultimo_servicio' => 'date',
    ];

    /**
     * Devuelve el estado normalizado para la vista/controlador
     */
    public function getEstadoVisualAttribute(): string
    {
        $est = strtolower(trim($this->estado ?? ''));

        if (in_array($est, ['de baja', 'de_baja', 'debaja'])) {
            return 'de_baja';
        }

        if ($est === 'vencido') {
            return 'vencido';
        }

        if ($est === 'por_vencer' || $est === 'por vencer') {
            return 'por_vencer';
        }

        if ($est === 'vigente') {
            return 'vigente';
        }

        // Si no tiene estado manual guardado, evalúa por fecha de PH
        if ($this->vencimiento_ph && $this->vencimiento_ph->isPast()) {
            return 'vencido';
        }

        return 'vigente';
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'codigo_cliente', 'codigo_cliente');
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class, 'codigo_sede', 'codigo_sede');
    }
}