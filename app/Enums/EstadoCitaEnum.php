<?php

namespace App\Enums;

enum EstadoCitaEnum: string
{
    case ACEPTADA = "aceptada";
    case CANCELADA = "cancelada";
    case PENDIENTE = "pendiente";
    case RECHAZADA = "rechazada";
    case FINALIZADA = "finalizada";
}
