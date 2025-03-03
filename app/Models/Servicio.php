<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Servicio extends Model
{
    use HasFactory;

    protected $table = 'servicios';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'descripcion',
        'costo_base',
        'estado',
    ];

    protected $casts = [
        'costo_base' => 'decimal:2',
    ];

    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = mb_strtoupper($value, 'UTF-8');
    }
}
