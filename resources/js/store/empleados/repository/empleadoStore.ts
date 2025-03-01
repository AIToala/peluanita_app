import axiosIns from '@axios';
import { acceptHMRUpdate, defineStore } from 'pinia';

export const useEmpleadoStore = defineStore('empleado', () => {
    async function fetchEmpleados(params?: any) {
        return await axiosIns.get('api/auth/dashboard/usuarios/listado', {
            params,
            headers: {
                'content-type': 'application/json',
            },
        });
    }
    return {
        fetchEmpleados,
    };
});
if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useEmpleadoStore, import.meta.hot));
}
