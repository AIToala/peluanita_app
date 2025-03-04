<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import Button from '@/components/ui/button/Button.vue';
import DataTable from '@/components/ui/DataTable.vue';
import { CitaColumns, useAgendaStore } from '@/store/agendas';
import { Link } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { h, nextTick, onMounted, ref } from 'vue';
import Dashboard from '../Dashboard.vue';

const citaStore = useAgendaStore();

interface SearchParams {
    id: string;
    value: string | number;
}

const citas = ref([]);
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
    loadCitas({
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

CitaColumns.forEach((column) => {
    if (column.id === 'estado') {
        column.cell = ({ row }) => {
            h(
                'div',
                {
                    class: 'flex items-center justify-center gap-2',
                },
                h(
                    Badge,
                    {
                        class: `text-white ${row.getValue('estado') === 'pendiente' ? 'bg-yellow-500 hover:bg-yellow-500/50' : row.getValue('estado') === 'rechazada' || row.getValue('estado') === 'cancelada' ? 'bg-red-500 hover:bg-red-500/50' : 'bg-green-500 hover:bg-green-500/50'}`,
                    },
                    row.getValue('estado'),
                ),
            );
        };
    }
});

const loadCitas = async (
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
        const response = await citaStore
            .fetchCitas({
                paginated: 1,
                ...payload.data,
                ...payload.pagination,
                with_cliente: 1,
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
            citas.value = res.data || [];
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
    loadCitas();
});
</script>

<template>
    <Dashboard title="Gestionar Citas">
        <template #content>
            <div
                class="h-full w-auto max-w-[100vw] flex-1 flex-col space-y-8 bg-white p-8"
            >
                <div class="flex items-center justify-between space-y-2">
                    <h1 class="font-serif text-2xl font-bold">
                        Gestionar Citas
                    </h1>
                </div>
                <div
                    class="grid w-full grid-cols-1 items-center gap-4 md:grid-cols-3"
                >
                    <div class="col-span-1 flex flex-col gap-2"></div>
                </div>
                <div
                    class="flex w-full items-center justify-center gap-4 sm:justify-end"
                >
                    <Button
                        class="w-full bg-green-500 text-white hover:bg-green-500/80 sm:w-fit"
                        as-child
                    >
                        <Link href="#">Agendar cita</Link>
                    </Button>
                </div>
                <DataTable
                    :data="citas"
                    :key="citas.length"
                    :columns="CitaColumns"
                    :columnFilters="columnFilters"
                    :current_page="currentPage"
                    :per_page="perPage"
                    :last_page="totalPages"
                    :axiosCall="loadCitas"
                />
            </div>
        </template>
    </Dashboard>
</template>
