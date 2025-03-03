<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import DataTable from '@/components/ui/DataTable.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    ClienteColumns,
    activar,
    desactivar,
} from '@/store/clientes/domain/ClienteColumns';
import { useClienteStore } from '@/store/clientes/index';
import { Link } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { h, nextTick, onMounted, ref } from 'vue';
import Dashboard from '../Dashboard.vue';

const clienteStore = useClienteStore();

interface SearchParams {
    id: string;
    value: string | number;
}

const clientes = ref([]);
const totalRows = ref(0);
const currentPage = ref(1);
const perPage = ref(10);
const totalPages = ref(0);

const columnFilters = ref<{ id: string; value: string | number }[]>([]);
const searchQuery = ref<SearchParams[]>([]);
const updateSearchQuery = (e: { target: { value: any; id: any } }) => {
    const searchValue = e.target.value;
    const id = e.target.id;
    const search = { id, value: searchValue };
    const existingSearchIndex = searchQuery.value.findIndex(
        (item) => item.id === id,
    );
    if (existingSearchIndex !== -1) {
        searchQuery.value[existingSearchIndex].value = searchValue;
    } else {
        searchQuery.value.push(search);
    }
    loadClientes({
        data: (() => {
            const data: { [key: string]: string | number } = {};
            searchQuery.value.forEach(({ id, value }) => {
                data[id] = value;
            });
            return data;
        })(),
        pagination: {
            page: 1,
            per_page: perPage.value,
        },
    });
};

ClienteColumns.forEach((column) => {
    if (column.id === 'actions') {
        column.cell = ({ row }) =>
            h(
                'div',
                { class: 'flex w-[20%] items-center gap-2' },
                row.getValue('estado') == 0
                    ? [
                          h(
                              Button,
                              {
                                  class: 'bg-green-500 hover:bg-green-500/80',
                                  onClick: () =>
                                      activar(row.getValue('id'), clienteStore),
                              },
                              {
                                  default: () => ['Activar'],
                              },
                          ),
                      ]
                    : [
                          h(
                              Button,
                              {
                                  asChild: true,
                                  class: '!bg-gray-100 hover:!bg-gray-100/80 !text-black ',
                              },
                              {
                                  default: () =>
                                      h(
                                          Link,
                                          {
                                              href: route(
                                                  'dashboard.clientes.editar',
                                                  {
                                                      id: row.getValue('id'),
                                                  },
                                              ),
                                          },
                                          {
                                              default: () => ['Editar'],
                                          },
                                      ),
                              },
                          ),
                          h(
                              Button,
                              {
                                  class: '!bg-red-600 hover:!bg-red-600/80 !text-white ',
                                  onClick: () =>
                                      desactivar(
                                          row.getValue('id'),
                                          clienteStore,
                                      ),
                              },
                              {
                                  default: () => ['Eliminar'],
                              },
                          ),
                      ],
            );
    }
});

const loadClientes = async (
    payload: { data: {}; pagination: { page: number; per_page: number } } = {
        data: {},
        pagination: {
            page: 1,
            per_page: 10,
        },
    },
) => {
    try {
        Swal.fire({
            title: 'Cargando',
            text: 'Por favor espere',
            icon: 'info',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });
        await nextTick();
        const response = await clienteStore
            .fetchClientes({
                paginated: 1,
                ...payload.data,
                ...payload.pagination,
            })
            .catch(async (error) => {
                await Swal.fire({
                    title: 'Error',
                    text: error.message ?? '',
                    icon: 'error',
                    showConfirmButton: true,
                }).then(async (result: { isConfirmed: any }) => {
                    if (result.isConfirmed) {
                        console.error(error);
                    }
                });
            });
        if (response) {
            const res = response.data;
            clientes.value = res.data || [];
            totalRows.value = res.total || 0;
            totalPages.value = res.last_page || 0;
            currentPage.value = res.current_page || 1;
        }
        Swal.close();
    } catch (error) {
        console.error(error);
    }
};

onMounted(() => {
    loadClientes();
});
</script>

<template>
    <Dashboard>
        <template #content>
            <div
                class="h-full w-auto max-w-[100vw] flex-1 flex-col space-y-8 bg-white p-8"
            >
                <div class="flex items-center justify-between space-y-2">
                    <h1 class="font-serif text-2xl font-bold">
                        Gestionar Clientes
                    </h1>
                </div>
                <div
                    class="grid w-full grid-cols-1 items-center gap-4 md:grid-cols-3"
                >
                    <div class="col-span-1 flex flex-col gap-2">
                        <Label for="nombre">Nombre</Label>
                        <Input
                            id="nombre"
                            type="text"
                            placeholder="Buscar por nombre..."
                            class="pl-2"
                            @keydown.enter="updateSearchQuery"
                        />
                    </div>
                </div>
                <div
                    class="flex w-full items-center justify-center gap-4 sm:justify-end"
                >
                    <Button
                        class="w-full bg-green-500 text-white hover:bg-green-500/80 sm:w-fit"
                        as-child
                    >
                        <Link :href="route('dashboard.servicios.crear')"
                            >Nuevo Cliente</Link
                        >
                    </Button>
                </div>
                <DataTable
                    :data="clientes"
                    :key="clientes.length"
                    :columns="ClienteColumns"
                    :columnFilters="columnFilters"
                    :current_page="currentPage"
                    :per_page="perPage"
                    :last_page="totalPages"
                    :axiosCall="loadClientes"
                />
            </div>
        </template>
    </Dashboard>
</template>
