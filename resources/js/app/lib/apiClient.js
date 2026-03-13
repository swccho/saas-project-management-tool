import axios from 'axios';
import { useAuthStore } from '../stores/authStore';
import { handleApiError } from '../services/apiErrorHandler';

const apiClient = axios.create({
  baseURL: '/api/v1',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

apiClient.interceptors.request.use((config) => {
  const authStore = useAuthStore();
  if (authStore.token) {
    config.headers.Authorization = `Bearer ${authStore.token}`;
  }
  return config;
});

apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      const authStore = useAuthStore();
      authStore.logout();
      window.location.href = '/login';
      return Promise.reject(error);
    }
    const isAuthEndpoint = /\/auth\/(login|register)$/.test(error.config?.url || '');
    handleApiError(error, { suppressToast: error.config?.skipGlobalErrorToast || isAuthEndpoint });
    return Promise.reject(error);
  }
);

export default apiClient;
