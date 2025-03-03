<script setup lang="ts">
import type { SidebarProps } from '@/components/ui/sidebar';
import { usePage } from '@inertiajs/vue3';

import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarRail,
} from '@/components/ui/sidebar';

import {
    BookOpen,
    BookUser,
    IdCard,
    Settings2,
    UsersRound,
} from 'lucide-vue-next';
import { computed } from 'vue';

const props = withDefaults(defineProps<SidebarProps>(), {
    collapsible: 'offcanvas',
});

const userAuth = computed(() => {
    const user = usePage().props.auth.user;
    return {
        name: user.name,
        email: user.email,
        avatar: '',
        role: user.role[0] ?? null,
    };
});

const nav = computed(() => {
    return userAuth.value.role === 'admin'
        ? data.navAdmin
        : userAuth.value.role === 'empleado'
          ? data.navEmpleados
          : data.navCliente;
});

const data = {
    navAdmin: [
        {
            title: 'Empleados',
            url: '#',
            icon: IdCard,
            isActive: true,
            items: [
                {
                    title: 'Crear Empleado',
                    url: route('dashboard.empleados.crear'),
                },
                {
                    title: 'Manejar Empleados',
                    url: route('dashboard.empleados'),
                },
            ],
        },
        {
            title: 'Clientes',
            url: '#',
            icon: UsersRound,
            items: [
                {
                    title: 'Crear Cliente',
                    url: '#',
                },
                {
                    title: 'Manejar Clientes',
                    url: '#',
                },
            ],
        },
        {
            title: 'Citas',
            url: '#',
            icon: BookUser,
            items: [
                {
                    title: 'Agendar Cita',
                    url: '#',
                },
                {
                    title: 'Manejar Citas',
                    url: '#',
                },
                {
                    title: 'Atenciones Realizadas',
                    url: '#',
                },
            ],
        },
        {
            title: 'Servicios',
            url: '#',
            icon: Settings2,
            items: [
                {
                    title: 'Crear Servicio',
                    url: '#',
                },
                {
                    title: 'Manejar Servicios',
                    url: '#',
                },
            ],
        },
        {
            title: 'Reportes',
            url: '#',
            icon: BookOpen,
            items: [
                {
                    title: 'Reporte de Citas',
                    url: '#',
                },
                {
                    title: 'Reporte de Atenciones',
                    url: '#',
                },
            ],
        },
    ],
    navEmpleados: [
        {
            title: 'Clientes',
            url: '#',
            icon: UsersRound,
            items: [
                {
                    title: 'Crear Cliente',
                    url: '#',
                },
                {
                    title: 'Manejar Clientes',
                    url: '#',
                },
            ],
        },
        {
            title: 'Citas',
            url: '#',
            icon: BookUser,
            items: [
                {
                    title: 'Agendar Cita',
                    url: '#',
                },
                {
                    title: 'Manejar Citas',
                    url: '#',
                },
                {
                    title: 'Atenciones Realizadas',
                    url: '#',
                },
            ],
        },
        {
            title: 'Reportes',
            url: '#',
            icon: BookOpen,
            items: [
                {
                    title: 'Reporte de Citas',
                    url: '#',
                },
                {
                    title: 'Reporte de Atenciones',
                    url: '#',
                },
            ],
        },
    ],
    navCliente: [
        {
            title: 'Citas',
            url: '#',
            icon: BookUser,
            items: [
                {
                    title: 'Agendar Cita',
                    url: '#',
                },
                {
                    title: 'Manejar Citas',
                    url: '#',
                },
                {
                    title: 'Atenciones Realizadas',
                    url: '#',
                },
            ],
        },
    ],
};
</script>

<template>
    <Sidebar v-bind="props">
        <SidebarHeader class="mb-0 mt-4 flex items-center justify-center pb-0">
            <h1
                class="cursor-pointer font-title text-4xl font-semibold text-emerald-500"
            >
                <a href="/dashboard">Peluanita</a>
            </h1>
        </SidebarHeader>
        <SidebarContent>
            <NavMain :items="nav" />
        </SidebarContent>
        <SidebarFooter>
            <NavUser :user="userAuth" />
        </SidebarFooter>
        <SidebarRail />
    </Sidebar>
</template>
