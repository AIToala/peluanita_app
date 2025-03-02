<script setup lang="ts">
import DataTable from '@/components/ui/DataTable.vue';
import { Input } from '@/components/ui/input';
import { UsuarioColumns } from '@/components/ui/UsuarioColumns';
import { useEmpleadoStore } from '@/store/empleados';
import { onMounted, ref } from 'vue';
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
    console.log(searchQuery.value);
};

const loadEmpleados = async () => {
    try {
        const response = await empleadoStore
            .fetchEmpleados({
                page: 1,
                perPage: 10,
            })
            .then((response) => {
                const res = response.data;
                empleados.value = res.data;
            })
            .catch((error) => {
                console.log(error);
            });
    } catch (error) {
        console.log(error);
    }
};

onMounted(() => {
    loadEmpleados();
});
</script>

<template>
    <Dashboard>
        <template #content>
            <div class="h-full w-full flex-1 flex-col space-y-8 p-8">
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
