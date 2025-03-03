import axiosIns from '@axios';
import { acceptHMRUpdate, defineStore } from 'pinia';

export const useEmpleadoStore = defineStore('empleado', () => {
    async function fetchEmpleados(params?: any) {
        return await axiosIns.get(route('usuarios.index'), {
            params,
            headers: {
                'content-type': 'application/json',
            },
        });
    }
    async function createEmpleado(data: any) {
        return await axiosIns.post(route('usuarios.store'), data);
    }
    async function updateEmpleado(data: any) {
        return await axiosIns.patch(route('usuarios.update', data.id), data);
    }
    async function deleteEmpleado(id: number) {
        return await axiosIns.delete(
            `api/auth/dashboard/usuarios/eliminar/${id}`,
        );
    }
    async function activarEmpleado(id: number) {
        return await axiosIns.post(`api/auth/dashboard/usuarios/activar/${id}`);
    }
    return {
        fetchEmpleados,
        createEmpleado,
        updateEmpleado,
        deleteEmpleado,
        activarEmpleado,
    };
});
if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useEmpleadoStore, import.meta.hot));
}
