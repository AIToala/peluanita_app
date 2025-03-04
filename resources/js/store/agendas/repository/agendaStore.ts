import axiosIns from '@axios';
import { acceptHMRUpdate, defineStore } from 'pinia';

export const useAgendaStore = defineStore('agenda', () => {
    async function fetchCitas(params?: any) {
        return await axiosIns.get(route('citas.index'), {
            params,
            headers: {
                'content-type': 'application/json',
            },
        });
    }
    async function createCita(data: any) {
        return await axiosIns.post(route('citas.create'), data);
    }
    async function updateCita(data: any) {
        return await axiosIns.patch(route('citas.update', data.id), data);
    }
    async function deleteCita(id: number) {
        return await axiosIns.delete(
            `api/auth/dashboard/agendas/citas/eliminar/${id}`,
        );
    }
    return {
        fetchCitas,
        createCita,
        updateCita,
        deleteCita,
    };
});
if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useAgendaStore, import.meta.hot));
}
