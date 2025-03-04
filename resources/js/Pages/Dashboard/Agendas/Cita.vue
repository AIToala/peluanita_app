<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Label from '@/components/ui/label/Label.vue';
import Dashboard from '@/Pages/Dashboard/Dashboard.vue';
import { useAgendaStore } from '@/store/agendas';
import { router, useForm, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { computed, nextTick } from 'vue';

import { Button } from '@/components/ui/button';

import { Calendar } from '@/components/ui/calendar';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import {
    CalendarDate,
    DateFormatter,
    type DateValue,
} from '@internationalized/date';
import { CalendarIcon } from 'lucide-vue-next';
import { ref } from 'vue';

const df = new DateFormatter('en-US', {
    dateStyle: 'long',
});

const date = ref<DateValue>();

const agendaStore = useAgendaStore();

const userAuth = computed(() => {
    const user = usePage().props.auth.user;
    if (user.role[0] === 'cliente') {
        return {
            id: user.id,
            name: user.name,
            email: user.email,
            role: user.role[0],
        };
    }
    return null;
});
const form = useForm({
    id_cliente: '',
    fecha: '',
    hora: '',
    estado: 'pendiente',
});

const submit = async () => {
    await Swal.fire({
        title: '¿Esta seguro de agenda esta cita?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar',
    }).then(async (result: { isConfirmed: any }) => {
        if (result.isConfirmed) {
            await nextTick();
            await agendaStore
                .createCita({
                    id_cliente: userAuth.value?.id ?? form.id_cliente,
                    fecha: form.fecha,
                    hora: form.hora,
                    estado: form.estado,
                })
                .then(async () => {
                    await Swal.fire({
                        title: 'Cliente creado con éxito',
                        icon: 'success',
                        showConfirmButton: true,
                    }).then(() => {
                        router.get(route('dashboard.clientes'));
                    });
                })
                .catch(async (error) => {
                    let err: any = error.error;
                    if (Array.isArray(err)) {
                        err.forEach((element: any) => {
                            form.setError(element[0], element[1]);
                        });
                        err = err.join(', ');
                    }
                    await Swal.fire({
                        title: 'Error al crear el cliente',
                        text: err ?? error.message ?? '',
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
</script>

<template>
    <Dashboard title="Cita" :sidebarOpen="false">
        <template #content>
            <div
                class="flex h-full w-full max-w-[100vw] flex-1 flex-col items-center justify-center space-y-12 bg-white p-8"
            >
                <h1
                    class="w-fit font-serif text-4xl font-semibold text-gray-900"
                >
                    Agendar Cita
                </h1>
                <form
                    @submit.prevent="submit"
                    class="mt-6 flex w-fit flex-col items-center justify-center space-y-6 rounded-lg bg-[#FCEEDA]/50 px-12 py-8 shadow-lg shadow-gray-400"
                >
                    <div class="mt-4">
                        <Label
                            class="font-serif text-2xl font-semibold text-gray-900"
                        >
                            Cita PeluAnita
                        </Label>
                    </div>
                    <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div
                            class="col-span-1 flex flex-col gap-2 md:col-span-2"
                        >
                            <InputLabel for="date" value="Fecha de Cita" />

                            <Popover>
                                <PopoverTrigger as-child>
                                    <Button
                                        variant="outline"
                                        class="justify-start text-left font-normal"
                                    >
                                        <CalendarIcon class="mr-2 h-4 w-4" />
                                        {{
                                            date
                                                ? new DateFormatter('es-EC', {
                                                      dateStyle: 'long',
                                                  }).format(
                                                      date.toDate(
                                                          'America/Guayaquil',
                                                      ),
                                                  )
                                                : 'Selecciona una fecha'
                                        }}
                                    </Button>
                                </PopoverTrigger>
                                <PopoverContent class="w-auto p-0">
                                    <Calendar
                                        v-model="date"
                                        locale="es-ES"
                                        initial-focus
                                        :min-value="
                                            new CalendarDate(
                                                new Date().getFullYear(),
                                                new Date().getMonth() + 1,
                                                new Date().getDate() + 1,
                                            )
                                        "
                                    />
                                </PopoverContent>
                            </Popover>

                            <InputError
                                class="mt-2"
                                :message="form.errors.fecha"
                            />
                        </div>
                        <div></div>
                    </div>
                    <div class="mt-4 flex items-center justify-end">
                        <PrimaryButton
                            class="ms-4"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Registrar
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </template>
    </Dashboard>
</template>
