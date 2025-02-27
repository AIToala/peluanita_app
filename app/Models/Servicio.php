<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Servicio extends Model
{
    use HasFactory;

    protected $table = 'servicios';
    protected $primaryKey = 'id_servicio';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'costo_base',
    ];
}
