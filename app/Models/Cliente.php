<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'id_usuario',
        'nombre',
        'apellido',
        'email',
        'telefono',
        'direccion',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }

    public function getNombreCompleto()
    {
        return mb_strtoupper($this->nombre) . ' ' . mb_strtoupper($this->apellido);
    }
}