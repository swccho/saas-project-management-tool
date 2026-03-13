import apiClient from '../lib/apiClient';

const unwrap = (res) => res.data?.data ?? res.data;

export const projectService = {
  async list(workspaceId) {
    const res = await apiClient.get(`/workspaces/${workspaceId}/projects`);
    return unwrap(res);
  },

  async get(workspaceId, projectId) {
    const res = await apiClient.get(`/workspaces/${workspaceId}/projects/${projectId}`);
    return unwrap(res);
  },

  async create(workspaceId, payload) {
    const res = await apiClient.post(`/workspaces/${workspaceId}/projects`, payload);
    return unwrap(res);
  },

  async update(workspaceId, projectId, payload) {
    const res = await apiClient.put(`/workspaces/${workspaceId}/projects/${projectId}`, payload);
    return unwrap(res);
  },

  async delete(workspaceId, projectId) {
    await apiClient.delete(`/workspaces/${workspaceId}/projects/${projectId}`);
  },

  async favorite(workspaceId, projectId) {
    const res = await apiClient.post(`/workspaces/${workspaceId}/projects/${projectId}/favorite`);
    return unwrap(res);
  },

  async unfavorite(workspaceId, projectId) {
    const res = await apiClient.delete(`/workspaces/${workspaceId}/projects/${projectId}/favorite`);
    return unwrap(res);
  },

  async listFavorites(workspaceId) {
    const res = await apiClient.get(`/workspaces/${workspaceId}/favorites`);
    return unwrap(res);
  },
};
