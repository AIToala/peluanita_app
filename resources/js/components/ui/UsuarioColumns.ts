import type { ColumnDef } from '@tanstack/vue-table';
import type { Task } from './UsuarioSchema';

import { Button } from '@/components/ui/button';
import { h } from 'vue';
import DataTableColumnHeader from '../DataTableColumnHeader.vue';
import { estados } from './data';

export const UsuarioColumns: ColumnDef<Task>[] = [
    {
        accessorKey: 'id',
        header: ({ column }) =>
            h(DataTableColumnHeader, { column, title: 'Id' }),
        cell: ({ row }) => h('div', { class: 'w-[20%]' }, row.getValue('id')),
        enableSorting: false,
        enableHiding: false,
    },
    {
        accessorKey: 'name',
        header: ({ column }) =>
            h(DataTableColumnHeader, { column, title: 'Nombre' }),

        cell: ({ row }) => {
            return h('div', { class: 'w-[20%]' }, row.getValue('name'));
        },
    },
    {
        accessorKey: 'email',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                class: 'w-[20%]',
                column,
                title: 'Email',
            }),

        cell: ({ row }) => {
            return h('div', { class: 'w-[20%]' }, row.getValue('email'));
        },
    },
    {
        accessorKey: 'estado',
        header: ({ column }) =>
            h(DataTableColumnHeader, { column, title: 'Estado' }),

        cell: ({ row }) => {
            const estado = row.getValue('estado') == 1 ? 'activo' : 'inactivo';
            const status = estados.find((status) => status.value === estado);

            if (!status) return null;

            return h('div', { class: 'flex w-[20%] items-center' }, [
                status.icon &&
                    h(status.icon, {
                        class: `mr-2 h-4 w-4`,
                    }),
                h('span', status.label),
            ]);
        },
        filterFn: (row, id, value) => {
            return value.includes(row.getValue(id));
        },
    },
    {
        id: 'actions',
        header: () => h('div', { class: 'w-[20%]' }, 'Acciones'),
        cell: ({ row }) =>
            h('div', { class: 'flex w-[20%] items-center gap-2' }, [
                h(Button, {}, { default: () => 'Editar' }),
                h(
                    Button,
                    { variant: 'destructive' },
                    { default: () => 'Eliminar' },
                ),
            ]),
    },
];
