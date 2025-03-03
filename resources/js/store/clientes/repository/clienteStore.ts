import axiosIns from '@axios';
import { acceptHMRUpdate, defineStore } from 'pinia';

export const useClienteStore = defineStore('empleado', () => {
    async function fetchClientes(params?: any) {
        return await axiosIns.get(route('clientes.index'), {
            params,
            headers: {
                'content-type': 'application/json',
            },
        });
    }
    async function createCliente(data: any) {
        return await axiosIns.post(route('clientes.store'), data);
    }
    async function updateCliente(data: any) {
        return await axiosIns.patch(route('clientes.update', data.id), data);
    }
    async function deleteCliente(id: number) {
        return await axiosIns.delete(
            `api/auth/dashboard/clientes/eliminar/${id}`,
        );
    }
    async function activarCliente(id: number) {
        return await axiosIns.post(`api/auth/dashboard/clientes/activar/${id}`);
    }
    return {
        fetchClientes,
        createCliente,
        updateCliente,
        deleteCliente,
        activarCliente,
    };
});
if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useClienteStore, import.meta.hot));
}
