import type { Cliente } from '@/store/clientes';
import type { ColumnDef } from '@tanstack/vue-table';

import DataTableColumnHeader from '@/components/DataTableColumnHeader.vue';
import { estados } from '@/components/ui/data';
import { useClienteStore } from '@/store/clientes';
import { router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { h, nextTick } from 'vue';

export const desactivar = async (
    id: string,
    clienteStore: ReturnType<typeof useClienteStore>,
) => {
    await Swal.fire({
        title: '¿Esta seguro de eliminar este cliente?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar',
    }).then(async (result: { isConfirmed: any }) => {
        if (result.isConfirmed) {
            await nextTick();
            await clienteStore
                .deleteCliente(parseInt(id))
                .then(async () => {
                    await Swal.fire({
                        title: 'Cliente eliminado con éxito',
                        icon: 'success',
                        showConfirmButton: true,
                    }).then(() => {
                        router.get(route('dashboard.clientes'));
                    });
                })
                .catch(async (error) => {
                    await Swal.fire({
                        title: 'Error al eliminar al cliente',
                        text: error.message ?? '',
                        icon: 'error',
                        showConfirmButton: true,
                    }).then((result: { isConfirmed: any }) => {
                        if (result.isConfirmed) {
                            console.error(error);
                        }
                    });
                });
        }
    });
};

export const activar = async (
    id: string,
    clienteStore: ReturnType<typeof useClienteStore>,
) => {
    await Swal.fire({
        title: '¿Esta seguro de activar este cliente?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar',
    }).then(async (result: { isConfirmed: any }) => {
        if (result.isConfirmed) {
            await nextTick();
            await clienteStore
                .activarCliente(parseInt(id))
                .then(async () => {
                    await Swal.fire({
                        title: 'Cliente activado con éxito',
                        icon: 'success',
                        showConfirmButton: true,
                    }).then(() => {
                        router.get(route('dashboard.clientes'));
                    });
                })
                .catch(async (error) => {
                    await Swal.fire({
                        title: 'Error al activar al cliente',
                        text: error.message ?? '',
                        icon: 'error',
                        showConfirmButton: true,
                    }).then((result: { isConfirmed: any }) => {
                        if (result.isConfirmed) {
                            console.error(error);
                        }
                    });
                });
        }
    });
};

export const ClienteColumns: ColumnDef<Cliente>[] = [
    {
        accessorKey: 'id',
        header: ({ column }) =>
            h(DataTableColumnHeader, { column, title: 'Id' }),
        cell: ({ row }) => h('div', { class: 'w-full' }, row.getValue('id')),
        enableSorting: false,
        enableHiding: false,
    },
    {
        accessorKey: 'nombre',
        header: ({ column }) =>
            h(DataTableColumnHeader, { column, title: 'Nombre' }),

        cell: ({ row }) => {
            return h(
                'div',
                { class: 'w-full' },
                row.getValue('nombre') + ' ' + row.original?.apellido,
            );
        },
    },
    {
        accessorKey: 'email',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                class: 'w-full',
                column,
                title: 'Email',
            }),

        cell: ({ row }) => {
            return h('div', { class: 'w-full' }, row.getValue('email'));
        },
    },
    {
        accessorKey: 'telefono',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                class: 'w-full',
                column,
                title: 'Contacto',
            }),

        cell: ({ row }) => {
            return h('div', { class: 'w-full' }, row.getValue('telefono'));
        },
    },
    {
        accessorKey: 'direccion',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                class: 'w-full',
                column,
                title: 'Dirección',
            }),

        cell: ({ row }) => {
            return h('div', { class: 'w-full' }, row.getValue('direccion'));
        },
    },

    {
        accessorKey: 'estado',
        header: ({ column }) =>
            h(DataTableColumnHeader, { column, title: 'Estado' }),

        cell: ({ row }) => {
            const estado =
                row.original.usuario?.estado === 1 ? 'activo' : 'inactivo';
            const status = estados.find((status) => status.value === estado);

            if (!status) return null;

            return h('div', { class: 'flex w-full items-center' }, [
                status.icon &&
                    h(status.icon, {
                        class: `mr-2 h-6 w-8 text-${estado == 'activo' ? 'green' : 'gray'}-600 rounded-full border-none}`,
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
        header: () => h('div', { class: 'w-full' }, 'Acciones'),
        cell: () => {},
    },
];
