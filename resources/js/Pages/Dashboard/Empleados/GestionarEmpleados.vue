<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import DataTable from '@/components/ui/DataTable.vue';
import { Input } from '@/components/ui/input';
import {
    UsuarioColumns,
    activar,
    desactivar,
} from '@/components/ui/UsuarioColumns';
import { useEmpleadoStore } from '@/store/empleados';
import { Link } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { h, nextTick, onMounted, ref } from 'vue';
import Dashboard from '../Dashboard.vue';

const empleadoStore = useEmpleadoStore();

interface SearchParams {
    id: string;
    value: string | number;
}

const empleados = ref([]);
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
};

UsuarioColumns.forEach((column) => {
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
                                      activar(
                                          row.getValue('id'),
                                          empleadoStore,
                                      ),
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
                              },
                              {
                                  default: () =>
                                      h(
                                          Link,
                                          {
                                              href: route(
                                                  'dashboard.empleados.editar',
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
                                  variant: 'destructive',
                                  onClick: () =>
                                      desactivar(
                                          row.getValue('id'),
                                          empleadoStore,
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

const loadEmpleados = async () => {
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
        await empleadoStore
            .fetchEmpleados({
                role: 'empleado',
                page: 1,
                perPage: 10,
            })
            .then((response) => {
                const res = response.data;
                empleados.value = res.data;
                Swal.close();
            })
            .catch((error) => {
                console.error(error);
            });
    } catch (error) {
        console.error(error);
    }
};

onMounted(() => {
    loadEmpleados();
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
                        Gestionar Empleados
                    </h1>
                </div>
                <div
                    class="grid w-full grid-cols-2 items-center lg:grid-cols-3"
                >
                    <Input
                        id="name"
                        type="text"
                        placeholder="Buscar por nombre..."
                        class="pl-2"
                        @keydown.enter="updateSearchQuery"
                    />
                </div>
                <DataTable
                    :data="empleados"
                    :columns="UsuarioColumns"
                    :columnFilters="columnFilters"
                    :searchQuery="searchQuery"
                />
            </div>
        </template>
    </Dashboard>
</template>
