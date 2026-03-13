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

  async updateMemberRole(workspaceId, memberId, role) {
    const res = await apiClient.put(`/workspaces/${workspaceId}/members/${memberId}/role`, { role });
    return res.data?.data ?? res.data;
  },

  async removeMember(workspaceId, memberId) {
    await apiClient.delete(`/workspaces/${workspaceId}/members/${memberId}`);
  },

  async transferOwnership(workspaceId, newOwnerId) {
    await apiClient.post(`/workspaces/${workspaceId}/owner-transfer`, { new_owner_id: newOwnerId });
  },

  async getPreferences(id) {
    const res = await apiClient.get(`/workspaces/${id}/preferences`);
    return unwrap(res);
  },

  async updatePreferences(id, payload) {
    const res = await apiClient.put(`/workspaces/${id}/preferences`, payload);
    return unwrap(res);
  },

  async getBranding(id) {
    const res = await apiClient.get(`/workspaces/${id}/branding`);
    return unwrap(res);
  },

  async updateBranding(id, payload) {
    const res = await apiClient.put(`/workspaces/${id}/branding`, payload);
    return unwrap(res);
  },

  async uploadLogo(id, file) {
    const formData = new FormData();
    formData.append('logo', file);
    const res = await apiClient.post(`/workspaces/${id}/branding/logo`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    return unwrap(res);
  },

  async uploadIcon(id, file) {
    const formData = new FormData();
    formData.append('icon', file);
    const res = await apiClient.post(`/workspaces/${id}/branding/icon`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    return unwrap(res);
  },

  async removeLogo(id) {
    await apiClient.delete(`/workspaces/${id}/branding/logo`);
  },

  async removeIcon(id) {
    await apiClient.delete(`/workspaces/${id}/branding/icon`);
  },
};
