import axios from 'axios';

const axiosIns = axios.create({
    baseURL: `http://localhost:8000`,
    withCredentials: true,
    xsrfCookieName: 'XSRF-TOKEN',
    xsrfHeaderName: 'X-XSRF-TOKEN',
    headers: {
        Accept: 'application/json',
    },
    withXSRFToken: true,
});

axiosIns.interceptors.request.use((config) => {
    const token = localStorage.getItem('api_token'); // Retrieve the token from localStorage
    if (token) {
        config.headers['Authorization'] = `Bearer ${token}`;
    }
    return config;
});

axiosIns.interceptors.response.use(
    (response) => {
        return response;
    },
    async (error) => {
        if (error.response.status === 419) {
            await axiosIns.get(route('sanctum/csrf-cookie'));
            return axiosIns.request(error.config);
        }
        return Promise.reject(error);
    },
);

export default axiosIns;
