<?php

namespace App\Enums;

enum EstadoCitaEnum: string
{
    case ACEPTADO = "aceptado";
    case CANCELADO = "cancelado";
    case PENDIENTE = "pendiente";
}
