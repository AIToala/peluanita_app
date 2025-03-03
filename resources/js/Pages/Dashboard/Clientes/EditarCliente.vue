<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Label from '@/components/ui/label/Label.vue';
import Dashboard from '@/Pages/Dashboard/Dashboard.vue';
import { useClienteStore } from '@/store/clientes/index';
import { router, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { nextTick, onMounted, ref } from 'vue';

const form = useForm({
    nombre: '',
    apellido: '',
    email: '',
    telefono: '',
    direccion: '',
    password: '',
    password_confirmation: '',
});

const clienteStore = useClienteStore();
const cliente = ref({
    nombre: '',
    apellido: '',
    email: '',
    id: '',
    telefono: '',
    direccion: '',
});

const getCliente = async (id: string) => {
    try {
        await nextTick();
        await clienteStore
            .fetchClientes({
                id_usuario: id,
                paginated: 0,
            })
            .then((response) => {
                cliente.value = response.data[0];
                form.nombre = cliente.value.nombre;
                form.apellido = cliente.value.apellido;
                form.email = cliente.value.email;
                form.telefono = cliente.value.telefono;
                form.direccion = cliente.value.direccion;
            })
            .catch((error) => {
                console.error(error);
                router.get(route('dashboard.clientes'));
            });
    } catch (error) {
        console.error(error);
    }
};

const submit = async () => {
    await Swal.fire({
        title: '¿Esta seguro de editar este cliente?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar',
    }).then(async (result: { isConfirmed: any }) => {
        if (result.isConfirmed) {
            await nextTick();
            await clienteStore
                .updateCliente({
                    id: cliente.value.id,
                    nombre: form.nombre,
                    apellido: form.apellido,
                    telefono: form.telefono,
                    direccion: form.direccion,
                    email: form.email,
                    password: form.password,
                    password_confirmation: form.password_confirmation,
                })
                .then(async () => {
                    await Swal.fire({
                        title: 'Cliente actualizado con éxito',
                        icon: 'success',
                        showConfirmButton: true,
                    }).then(() => {
                        router.get(route('dashboard.clientes'));
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
                        title: 'Error al actualizar el cliente',
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
    getCliente(route().params.id);
});
</script>

<template>
    <Dashboard>
        <template #content>
            <div
                class="h-full w-auto max-w-[100vw] flex-1 flex-col space-y-8 bg-white p-8"
            >
                <h1 class="font-serif text-2xl font-semibold text-gray-900">
                    Editar Cliente
                </h1>
                <form
                    @submit.prevent="submit"
                    class="mt-6 w-full space-y-6 rounded-lg bg-gray-50 px-6 py-4 shadow-lg"
                >
                    <div class="mt-4">
                        <Label
                            class="font-serif text-xl font-semibold text-gray-900"
                        >
                            Información de Usuario
                        </Label>
                    </div>
                    <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="col-span-1 md:col-span-2">
                            <InputLabel for="email" value="Email" />

                            <TextInput
                                id="email"
                                type="email"
                                class="mt-1 block w-full"
                                v-model="form.email"
                                required
                                autocomplete="username"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.email"
                            />
                        </div>

                        <div>
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

                        <div>
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
                    </div>
                    <div class="mt-4">
                        <Label
                            class="mt-4 font-serif text-xl font-semibold text-gray-900"
                            >Información Personal
                        </Label>
                    </div>
                    <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <InputLabel for="nombre" value="Nombre" />

                            <TextInput
                                id="nombre"
                                type="text"
                                class="mt-1 block w-full capitalize"
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
                            <InputLabel for="apellido" value="Apellido" />

                            <TextInput
                                id="apellido"
                                type="text"
                                class="mt-1 block w-full capitalize"
                                v-model="form.apellido"
                                required
                                autofocus
                                autocomplete="apellido"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.apellido"
                            />
                        </div>
                        <div>
                            <InputLabel for="telefono" value="Teléfono" />

                            <TextInput
                                id="telefono"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.telefono"
                                required
                                autofocus
                                autocomplete="telefono"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.telefono"
                            />
                        </div>
                        <div>
                            <InputLabel for="direccion" value="Dirección" />

                            <TextInput
                                id="direccion"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.direccion"
                                required
                                autofocus
                                autocomplete="direccion"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.direccion"
                            />
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-end">
                        <PrimaryButton
                            class="ms-4"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Guardar
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </template>
    </Dashboard>
</template>
