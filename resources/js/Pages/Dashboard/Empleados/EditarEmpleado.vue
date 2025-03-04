<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Dashboard from '@/Pages/Dashboard/Dashboard.vue';
import { useEmpleadoStore } from '@/store/empleados';
import { router, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { nextTick, onMounted, ref } from 'vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'empleado',
});

const passwordInput = ref<HTMLInputElement | null>(null);

const empleadoStore = useEmpleadoStore();
const empleado = ref({ name: '', email: '', id: '' });

const getEmpleado = async (id: string) => {
    try {
        await nextTick();
        await empleadoStore
            .fetchEmpleados({
                id_usuario: id,
                paginated: 0,
            })
            .then((response) => {
                empleado.value = response.data[0];
                form.name = empleado.value.name;
                form.email = empleado.value.email;
            })
            .catch((error) => {
                console.error(error);
                router.get(route('dashboard.empleados'));
            });
    } catch (error) {
        console.error(error);
    }
};

const submit = async () => {
    await Swal.fire({
        title: '¿Esta seguro de editar este empleado?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar',
    }).then(async (result: { isConfirmed: any }) => {
        if (result.isConfirmed) {
            await nextTick();
            await empleadoStore
                .updateEmpleado({
                    id: empleado.value.id,
                    name: form.name,
                    email: form.email,
                    password: form.password,
                    password_confirmation: form.password_confirmation,
                    role: form.role,
                })
                .then(async () => {
                    await Swal.fire({
                        title: 'Empleado actualizado con éxito',
                        icon: 'success',
                        showConfirmButton: true,
                    }).then(() => {
                        router.get(route('dashboard.empleados'));
                    });
                    form.reset('password', 'password_confirmation');
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
                        title: 'Error al actualizar el empleado',
                        text: err ?? error.message ?? '',
                        icon: 'error',
                        showConfirmButton: true,
                    }).then((result: { isConfirmed: any }) => {
                        if (result.isConfirmed) {
                            form.reset('password', 'password_confirmation');
                            console.error(error);
                        }
                    });
                });
        }
    });
};

onMounted(() => {
    getEmpleado(route().params.id);
});
</script>

<template>
    <Dashboard title="Editar Empleado">
        <template #content>
            <div
                class="h-full w-auto max-w-[100vw] flex-1 flex-col space-y-8 bg-white p-8"
            >
                <h1 class="font-serif text-2xl font-semibold text-gray-900">
                    Editar Empleado
                </h1>
                <form
                    @submit.prevent="submit"
                    class="mt-6 w-full space-y-6 rounded-lg bg-gray-50 px-6 py-4 shadow-lg"
                >
                    <div>
                        <InputLabel for="name" value="Nombre" />

                        <TextInput
                            id="name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                        />

                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>
                    <div>
                        <InputLabel for="email" value="Email" />

                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model="form.email"
                            required
                            autocomplete="username"
                        />

                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>
                    <div>
                        <InputLabel for="password" value="Nueva Contraseña" />

                        <TextInput
                            id="password"
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-full"
                            autocomplete="new-password"
                        />

                        <InputError
                            :message="form.errors.password"
                            class="mt-2"
                        />
                    </div>

                    <div>
                        <InputLabel
                            for="password_confirmation"
                            value="Confirmar Nueva Contraseña"
                        />

                        <TextInput
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            class="mt-1 block w-full"
                            autocomplete="new-password"
                        />

                        <InputError
                            :message="form.errors.password_confirmation"
                            class="mt-2"
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
