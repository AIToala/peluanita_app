import axios from 'axios';

const baseURL =
    import.meta.env.MODE === 'development' ||
    import.meta.env.VITE_APP_ENV === 'local'
        ? 'http://localhost:8000'
        : import.meta.env.VITE_APP_URL;

const axiosIns = axios.create({
    baseURL: baseURL,
    withCredentials: true,
    xsrfCookieName: 'XSRF-TOKEN',
    xsrfHeaderName: 'X-XSRF-TOKEN',
    headers: {
        'X-CSRF-TOKEN': document.head
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute('content'),
        Accept: 'application/json',
    },
});

axiosIns.interceptors.request.use((config) => {
    const token = localStorage.getItem('api_token'); // Retrieve the token from localStorage
    if (token) {
        config.headers['Authorization'] = `Bearer ${token}`;
    }
    if (config.headers['X-Inertia']) {
        config.headers['X-Inertia'] = 'true';
    }
    return config;
});

axiosIns.interceptors.response.use(
    (response) => {
        return response;
    },
    async (error) => {
        if (error.status === 419) {
            await axiosIns.get(route('sanctum/csrf-cookie'));
            return axiosIns.request(error.config);
        }
        return Promise.reject(error.response.data);
    },
);

export default axiosIns;
