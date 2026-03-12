import apiClient from '../lib/apiClient';

export const authService = {
  async register(name, email, password, password_confirmation) {
    const res = await apiClient.post('/auth/register', {
      name,
      email,
      password,
      password_confirmation,
    });
    return res.data?.data ?? res.data;
  },

  async login(email, password) {
    const res = await apiClient.post('/auth/login', { email, password });
    return res.data?.data ?? res.data;
  },

  async logout() {
    await apiClient.post('/auth/logout');
  },

  async fetchMe() {
    const res = await apiClient.get('/auth/me');
    return res.data?.data ?? res.data;
  },
};
