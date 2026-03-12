import apiClient from '../lib/apiClient';

const unwrap = (res) => res.data?.data ?? res.data;

export const labelService = {
  async list(workspaceId, projectId) {
    const res = await apiClient.get(`/workspaces/${workspaceId}/projects/${projectId}/labels`);
    return unwrap(res);
  },

  async create(workspaceId, projectId, payload) {
    const res = await apiClient.post(
      `/workspaces/${workspaceId}/projects/${projectId}/labels`,
      payload
    );
    return unwrap(res);
  },

  async update(workspaceId, projectId, labelId, payload) {
    const res = await apiClient.put(
      `/workspaces/${workspaceId}/projects/${projectId}/labels/${labelId}`,
      payload
    );
    return unwrap(res);
  },

  async delete(workspaceId, projectId, labelId) {
    await apiClient.delete(
      `/workspaces/${workspaceId}/projects/${projectId}/labels/${labelId}`
    );
  },
};
