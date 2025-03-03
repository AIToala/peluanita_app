import axiosIns from '@axios';
import { acceptHMRUpdate, defineStore } from 'pinia';

export const useServicioStore = defineStore('servicio', () => {
    async function fetchServicios(params?: any) {
        return await axiosIns.get(route('servicios.index'), {
            params,
            headers: {
                'content-type': 'application/json',
            },
        });
    }
    async function createServicio(data: any) {
        return await axiosIns.post(route('servicios.store'), data);
    }
    async function updateServicio(data: any) {
        return await axiosIns.patch(route('servicios.update', data.id), data);
    }
    async function deleteServicio(id: number) {
        return await axiosIns.delete(
            `api/auth/dashboard/servicios/eliminar/${id}`,
        );
    }
    async function activarServicio(id: number) {
        return await axiosIns.post(
            `api/auth/dashboard/servicios/activar/${id}`,
        );
    }
    return {
        fetchServicios,
        createServicio,
        updateServicio,
        deleteServicio,
        activarServicio,
    };
});
if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useServicioStore, import.meta.hot));
}
