<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Dashboard from '@/Pages/Dashboard/Dashboard.vue';
import { useEmpleadoStore } from '@/store/empleados';
import { router, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { nextTick } from 'vue';

const empleadoStore = useEmpleadoStore();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'empleado',
});

const submit = async () => {
    await Swal.fire({
        title: '¿Esta seguro de crear este empleado?',
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
                .createEmpleado({
                    name: form.name,
                    email: form.email,
                    password: form.password,
                    password_confirmation: form.password_confirmation,
                    role: form.role,
                })
                .then(async () => {
                    await Swal.fire({
                        title: 'Empleado creado con éxito',
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
                        title: 'Error al crear el empleado',
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
</script>

<template>
    <Dashboard title="Registrar Empleado">
        <template #content>
            <div
                class="h-full w-auto max-w-[100vw] flex-1 flex-col space-y-8 bg-white p-8"
            >
                <h1 class="font-serif text-2xl font-semibold text-gray-900">
                    Registrar Nuevo Empleado
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

                    <div class="mt-4">
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

                    <div class="mt-4">
                        <InputLabel for="password" value="Contraseña" />

                        <TextInput
                            id="password"
                            type="password"
                            class="mt-1 block w-full"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                        />

                        <InputError
                            class="mt-2"
                            :message="form.errors.password"
                        />
                    </div>

                    <div class="mt-4">
                        <InputLabel
                            for="password_confirmation"
                            value="Confirmar Contraseña"
                        />

                        <TextInput
                            id="password_confirmation"
                            type="password"
                            class="mt-1 block w-full"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.password_confirmation"
                        />
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
