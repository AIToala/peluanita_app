<script setup lang="ts">
import { useEmpleadoStore } from '@/store/empleados';
import { onMounted } from 'vue';
import Dashboard from '../Dashboard.vue';

const empleadoStore = useEmpleadoStore();

const loadEmpleados = async () => {
    await empleadoStore
        .fetchEmpleados()
        .then((response) => {
            console.log(response);
        })
        .catch((error) => {
            console.log(error);
        });
};

onMounted(() => {
    loadEmpleados();
});
</script>

<template>
    <Dashboard>
        <template #content>
            <h1>Gestionar Empleados</h1>
        </template>
    </Dashboard>
</template>
