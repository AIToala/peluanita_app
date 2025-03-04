<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Dashboard from '@/Pages/Dashboard/Dashboard.vue';
import { useServicioStore } from '@/store/servicios';
import { router, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { nextTick, onMounted, ref } from 'vue';

const form = useForm({
    nombre: '',
    descripcion: '',
    costo_base: '',
});

const servicioStore = useServicioStore();
const servicio = ref({
    id: '',
    nombre: '',
    descripcion: '',
    costo_base: '',
});

const getServicio = async (id_servicio: string) => {
    try {
        await nextTick();
        await servicioStore
            .fetchServicios({
                id_servicio,
                paginated: 0,
            })
            .then((response) => {
                servicio.value = response.data[0];
                form.nombre = servicio.value.nombre;
                form.descripcion = servicio.value.descripcion;
                form.costo_base = servicio.value.costo_base;
            })
            .catch((error) => {
                console.error(error);
                router.get(route('dashboard.servicios'));
            });
    } catch (error) {
        console.error(error);
    }
};
const formatCurrency = () => {
    if (!form.costo_base || form.costo_base.trim() === '') {
        form.costo_base = '0.00';
        return;
    }

    let money = form.costo_base.replace(/[^0-9.]/g, '');

    const parts = money.split('.');
    if (parts.length > 2) {
        money = parts[0] + '.' + parts.slice(1).join('');
    }

    const number = parseFloat(money);
    form.costo_base = isNaN(number)
        ? '0.00'
        : number.toLocaleString('en-US', {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2,
          });
    form.costo_base = form.costo_base.replace(/,/g, '');
};

const submit = async () => {
    await Swal.fire({
        title: '¿Esta seguro de editar este servicio?',
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
                .updateServicio({
                    id: servicio.value.id,
                    nombre: form.nombre,
                    descripcion: form.descripcion,
                })
                .then(async () => {
                    await Swal.fire({
                        title: 'Servicio actualizado con éxito',
                        icon: 'success',
                        showConfirmButton: true,
                    }).then((result: { isConfirmed: any }) => {
                        if (result.isConfirmed) {
                            router.get(route('dashboard.servicios'));
                        }
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
                        title: 'Error al editar el servicio',
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

onMounted(() => {
    getServicio(route().params.id);
});
</script>

<template>
    <Dashboard title="Editar Servicio">
        <template #content>
            <div
                class="h-full w-auto max-w-[100vw] flex-1 flex-col space-y-8 bg-white p-8"
            >
                <h1 class="font-serif text-2xl font-semibold text-gray-900">
                    Editar Servicio
                </h1>
                <form
                    @submit.prevent="submit"
                    class="mt-6 w-full space-y-6 rounded-lg bg-gray-50 px-6 py-4 shadow-lg"
                >
                    <div>
                        <InputLabel for="nombre" value="Nombre" />

                        <TextInput
                            id="nombre"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.nombre"
                            required
                            autofocus
                            autocomplete="nombre"
                        />

                        <InputError
                            class="mt-2"
                            :message="form.errors.nombre"
                        />
                    </div>
                    <div>
                        <InputLabel for="descripcion" value="descripcion" />

                        <TextInput
                            id="descripcion"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.descripcion"
                            required
                            autocomplete="descripcion"
                        />

                        <InputError
                            class="mt-2"
                            :message="form.errors.descripcion"
                        />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="costo_base" value="Precio" />

                        <TextInput
                            id="costo_base"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.costo_base"
                            required
                            autocomplete="costo_base"
                            @blur="formatCurrency"
                        />

                        <InputError
                            class="mt-2"
                            :message="form.errors.costo_base"
                        />
                    </div>

                    <div class="flex items-center gap-4">
                        <PrimaryButton :disabled="form.processing"
                            >Guardar</PrimaryButton
                        >

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p
                                v-if="form.recentlySuccessful"
                                class="text-sm text-gray-600"
                            >
                                Guardado.
                            </p>
                        </Transition>
                    </div>
                </form>
            </div>
        </template>
    </Dashboard>
</template>
