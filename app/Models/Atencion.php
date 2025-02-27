<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Atencion extends Model
{
    use HasFactory;

    protected $table = 'atenciones';
    protected $primaryKey = 'id_atencion';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'id_servicio',
        'id_cliente',
        'fecha_hora',
        'costo_final',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime:Y-m-d H:i:s',
    ];

    public function servicio()
    {
        return $this->hasOne(Servicio::class, 'id_servicio', 'id_servicio');
    }

    public function cliente()
    {
        return $this->hasOne(User::class, 'id_cliente', 'id');
    }
}
