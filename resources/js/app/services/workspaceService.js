import apiClient from '../lib/apiClient';

const unwrap = (res) => res.data?.data ?? res.data;

export const workspaceService = {
  async list() {
    const res = await apiClient.get('/workspaces');
    return unwrap(res);
  },

  async get(id) {
    const res = await apiClient.get(`/workspaces/${id}`);
    return unwrap(res);
  },

  async create(name) {
    const res = await apiClient.post('/workspaces', { name });
    return unwrap(res);
  },

  async update(id, payload) {
    const res = await apiClient.put(`/workspaces/${id}`, payload);
    return unwrap(res);
  },

  async delete(id) {
    await apiClient.delete(`/workspaces/${id}`);
  },

  async getMembers(id) {
    const res = await apiClient.get(`/workspaces/${id}/members`);
    return res.data?.data ?? res.data;
  },
};
