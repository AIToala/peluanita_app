import type { Servicio } from '@/store/servicios';
import type { ColumnDef } from '@tanstack/vue-table';

import DataTableColumnHeader from '@/components/DataTableColumnHeader.vue';
import { estados } from '@/components/ui/data';
import { useServicioStore } from '@/store/servicios';
import { router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { h, nextTick } from 'vue';

export const desactivarServicio = async (
    id: string,
    servicioStore: ReturnType<typeof useServicioStore>,
) => {
    await Swal.fire({
        title: '¿Esta seguro de eliminar este servicio?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar',
    }).then(async (result: { isConfirmed: any }) => {
        if (result.isConfirmed) {
            await nextTick();
            await servicioStore
                .deleteServicio(parseInt(id))
                .then(async () => {
                    await Swal.fire({
                        title: 'Servicio eliminado con éxito',
                        icon: 'success',
                        showConfirmButton: true,
                    }).then(() => {
                        router.get(route('dashboard.servicios'));
                    });
                })
                .catch(async (error) => {
                    await Swal.fire({
                        title: 'Error al eliminar al servicio',
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

export const activarServicio = async (
    id: string,
    servicioStore: ReturnType<typeof useServicioStore>,
) => {
    await Swal.fire({
        title: '¿Esta seguro de activar este servicio?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar',
    }).then(async (result: { isConfirmed: any }) => {
        if (result.isConfirmed) {
            await nextTick();
            await servicioStore
                .activarServicio(parseInt(id))
                .then(async () => {
                    await Swal.fire({
                        title: 'Servicio activado con éxito',
                        icon: 'success',
                        showConfirmButton: true,
                    }).then(() => {
                        router.get(route('dashboard.servicios'));
                    });
                })
                .catch(async (error) => {
                    await Swal.fire({
                        title: 'Error al activar al servicio',
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

export const ServicioColumns: ColumnDef<Servicio>[] = [
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
            return h('div', { class: 'w-full' }, row.getValue('nombre'));
        },
    },
    {
        accessorKey: 'descripcion',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                class: 'w-full',
                column,
                title: 'Descripción',
            }),

        cell: ({ row }) => {
            return h('div', { class: 'w-full' }, row.getValue('descripcion'));
        },
    },
    {
        accessorKey: 'costo_base',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                class: 'w-full',
                column,
                title: 'Precio $',
            }),

        cell: ({ row }) => {
            return h(
                'div',
                { class: 'w-full text-right' },
                parseFloat(row.getValue('costo_base')).toFixed(2),
            );
        },
        enableSorting: true,
        sortingFn: 'alphanumeric',
    },
    {
        accessorKey: 'estado',
        header: ({ column }) =>
            h(DataTableColumnHeader, { column, title: 'Estado' }),

        cell: ({ row }) => {
            const estado = row.getValue('estado') == 1 ? 'activo' : 'inactivo';
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
