import apiClient from '../lib/apiClient';

export const authService = {
  async register(name, email, password, password_confirmation, invitation_token = null) {
    const payload = {
      name,
      email,
      password,
      password_confirmation,
    };
    if (invitation_token) {
      payload.invitation_token = invitation_token;
    }
    const res = await apiClient.post('/auth/register', payload);
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
