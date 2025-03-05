<script setup lang="ts">
import Logo from '@/Components/Logo.vue';
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/vue3';
import { ChevronRight } from 'lucide-vue-next';

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
}>();
</script>

<template>
    <div
        class="flex min-h-screen flex-col bg-gray-50 text-black/50 dark:bg-gray-900 dark:text-neutral-50/50"
    >
        <header class="bg-[#FCDDC8]/50 shadow-md dark:bg-[#534F43]/25">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between py-2">
                    <Logo showText />
                    <nav v-if="canLogin" class="flex justify-center gap-2">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-neutral-50 dark:hover:text-white/80 dark:focus-visible:ring-white"
                        >
                            Agenda
                        </Link>

                        <template v-else>
                            <Button
                                as-child
                                class="bg-gray-50 text-gray-900 hover:bg-gray-50/50"
                            >
                                <Link :href="route('login')">
                                    Login
                                    <ChevronRight class="ml-2 h-4 w-4" />
                                </Link>
                            </Button>
                            <Button
                                class="bg-green-600 text-white hover:bg-green-600/75"
                                >Contacto</Button
                            >
                            <Button as-child>
                                <Link
                                    v-if="canRegister"
                                    :href="route('register')"
                                >
                                    Registrate
                                </Link>
                            </Button>
                        </template>
                    </nav>
                </div>
            </div>
        </header>
        <main class="min-h-[50vh]"></main>
        <footer
            class="py-16 text-center text-sm text-black dark:text-neutral-50/70"
        >
            <Logo logoSrc="footer" classNames="h-56 w-56 object-contain" />
        </footer>
    </div>
</template>
