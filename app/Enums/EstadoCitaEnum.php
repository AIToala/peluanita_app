<?php

namespace App\Enums;

enum EstadoCitaEnum: string
{
    case BORRADOR = "borrador";
    case ACEPTADO = "aceptado";
    case CANCELADO = "cancelado";
    case PENDIENTE = "pendiente";
    case RECHAZADO = "rechazado";
    case FINALIZADO = "finalizado";
}
