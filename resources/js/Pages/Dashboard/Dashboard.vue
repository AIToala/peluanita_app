<script lang="ts">
export const description = 'A sidebar that collapses to icons.';
export const iframeHeight = '800px';
export const containerClass = 'w-full h-full';
</script>
<script setup lang="ts">
import AppSidebar from '@/components/AppSidebar.vue';
import Logo from '@/Components/Logo.vue';
import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/components/ui/breadcrumb';
import { Separator } from '@/components/ui/separator';
import {
    SidebarInset,
    SidebarProvider,
    SidebarTrigger,
} from '@/components/ui/sidebar';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface DashboardProps {
    title: string;
    sidebarOpen: boolean;
}

const props = defineProps({
    title: {
        type: String,
        default: 'Dashboard',
    },
    sidebarOpen: {
        type: Boolean,
        default: true,
    },
}) as DashboardProps;

const userAuth = computed(() => {
    return usePage().props.auth.user;
});
const page = usePage();
const breadcrumbTitles: Record<string, string> = {
    Dashboard: 'Dashboard',
    Admin: 'Admin Panel',
    Clientes: 'Clientes',
    Empleados: 'Empleados',
};
const breadcrumbs = computed(() => {
    const segments = page.url.split('/').filter(Boolean); // Split URL into parts
    return segments.map((segment, index) => ({
        name: breadcrumbTitles[segment] || segment,
        path:
            index === segments.length - 1
                ? null
                : '/' + segments.slice(0, index + 1).join('/'),
    }));
});
</script>

<template>
    <Head :title="props.title" />
    <SidebarProvider :defaultOpen="props.sidebarOpen">
        <AppSidebar />
        <SidebarInset>
            <header
                class="flex h-16 shrink-0 items-center gap-2 shadow-sm transition-[width,height] ease-linear group-has-[[data-collapsible=icon]]/sidebar-wrapper:h-12 dark:bg-gray-900 dark:text-neutral-50 dark:shadow-none"
            >
                <div class="flex items-center gap-2 px-4">
                    <SidebarTrigger class="-ml-1" />
                    <Separator orientation="vertical" class="mr-2 h-4" />
                    <Breadcrumb>
                        <BreadcrumbList>
                            <BreadcrumbItem
                                v-for="(crumb, index) in breadcrumbs"
                                :key="index"
                            >
                                <template v-if="crumb.path">
                                    <BreadcrumbLink
                                        class="capitalize"
                                        :href="crumb.path"
                                    >
                                        {{ crumb.name }}
                                    </BreadcrumbLink>
                                    <BreadcrumbSeparator
                                        v-if="index !== breadcrumbs.length - 1"
                                    />
                                </template>
                                <template v-else>
                                    <BreadcrumbPage class="capitalize">{{
                                        crumb.name
                                    }}</BreadcrumbPage>
                                </template>
                            </BreadcrumbItem>
                        </BreadcrumbList>
                    </Breadcrumb>
                </div>
            </header>
            <div
                class="flex min-h-[80vh] w-auto max-w-[100vw] bg-white sm:justify-center dark:bg-gray-900"
            >
                <template v-if="!$slots.content">
                    <div
                        class="flex w-full flex-col items-center justify-center gap-4 p-4 font-serif"
                    >
                        <Logo logoSrc="footer" classNames="h-full w-64" />
                        <h2
                            class="text-2xl font-semibold text-gray-900 dark:text-neutral-50"
                        >
                            Bienvenido, {{ userAuth.name }}
                        </h2>
                    </div>
                </template>
                <slot name="content" />
            </div>
        </SidebarInset>
    </SidebarProvider>
</template>
