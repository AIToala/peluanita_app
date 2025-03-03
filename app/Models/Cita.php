<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cita extends Model
{
    use HasFactory;

    protected $table = 'citas';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'id_cliente',
        'fecha_hora_cita',
        'estado',
    ];

    protected $casts = [
        'fecha_hora_cita' => 'datetime:Y-m-d H:i:s',
    ];

    public function cliente()
    {
        return $this->belongsTo(User::class, 'id_cliente', 'id');
    }
}
