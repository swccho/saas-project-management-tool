import apiClient from '../lib/apiClient';

const unwrap = (res) => res.data?.data ?? res.data;

export const notificationService = {
  async list(params = {}) {
    const res = await apiClient.get('/notifications', { params });
    return unwrap(res);
  },

  async markRead(notificationId) {
    const res = await apiClient.put(`/notifications/${notificationId}/read`);
    return unwrap(res);
  },

  async markAllRead() {
    const res = await apiClient.put('/notifications/read-all');
    return unwrap(res);
  },
};
