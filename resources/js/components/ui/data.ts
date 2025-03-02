import { Circle } from 'lucide-vue-next';
import { h } from 'vue';

export const estadoCita = [
    {
        value: 'pendiente',
        label: 'Pendiente',
    },
    {
        value: 'aceptado',
        label: 'Aceptado',
    },
    {
        value: 'cancelado',
        label: 'Cancelado',
    },
    {
        value: 'rechazado',
        label: 'Rechazado',
    },
    {
        value: 'finalizado',
        label: 'Finalizado',
    },
    {
        value: 'borrador',
        label: 'Borrador',
    },
];

export const estados = [
    {
        value: 'activo',
        label: 'Activo',
        icon: h(Circle),
    },
    {
        value: 'inactivo',
        label: 'Inactivo',
        icon: h(Circle),
    },
];
