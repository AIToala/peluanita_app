import type { Cita } from '@/store/agendas';
import type { ColumnDef } from '@tanstack/vue-table';

import DataTableColumnHeader from '@/components/DataTableColumnHeader.vue';
import { h } from 'vue';

export const CitaColumns: ColumnDef<Cita>[] = [
    {
        accessorKey: 'id',
        header: ({ column }) =>
            h(DataTableColumnHeader, { column, title: 'Id' }),
        cell: ({ row }) => h('div', { class: 'w-full' }, row.getValue('id')),
        enableSorting: false,
        enableHiding: false,
    },
    {
        accessorKey: 'fecha_hora_cita',
        header: ({ column }) =>
            h(DataTableColumnHeader, { column, title: 'Fecha / Hora' }),

        cell: ({ row }) => {
            return h(
                'div',
                { class: 'w-full' },
                row.getValue('fecha_hora_cita'),
            );
        },
    },
    {
        accessorKey: 'estado',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                class: 'w-full',
                column,
                title: 'Estado',
            }),

        cell: () => {},
    },
    {
        id: 'actions',
        header: () => h('div', { class: 'w-full' }, 'Acciones'),
        cell: () => {},
    },
];
