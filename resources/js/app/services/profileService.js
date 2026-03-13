import apiClient from '../lib/apiClient';

const unwrap = (res) => res.data?.data ?? res.data;

export const profileService = {
  async get() {
    const res = await apiClient.get('/profile');
    return unwrap(res);
  },

  async update(payload) {
    const res = await apiClient.put('/profile', payload);
    return unwrap(res);
  },

  async updatePassword(currentPassword, password, passwordConfirmation) {
    const res = await apiClient.put('/profile/password', {
      current_password: currentPassword,
      password,
      password_confirmation: passwordConfirmation,
    });
    return unwrap(res);
  },

  async uploadAvatar(file) {
    const formData = new FormData();
    formData.append('avatar', file);
    const res = await apiClient.post('/profile/avatar', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    return unwrap(res);
  },
};
