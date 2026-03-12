import apiClient from '../lib/apiClient';

const unwrap = (res) => res.data?.data ?? res.data;

export const boardService = {
  async list(workspaceId, projectId) {
    const res = await apiClient.get(`/workspaces/${workspaceId}/projects/${projectId}/boards`);
    return unwrap(res);
  },

  async get(workspaceId, projectId, boardId) {
    const res = await apiClient.get(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}`
    );
    return unwrap(res);
  },

  async create(workspaceId, projectId, payload) {
    const res = await apiClient.post(
      `/workspaces/${workspaceId}/projects/${projectId}/boards`,
      payload
    );
    return unwrap(res);
  },

  async update(workspaceId, projectId, boardId, payload) {
    const res = await apiClient.put(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}`,
      payload
    );
    return unwrap(res);
  },

  async delete(workspaceId, projectId, boardId) {
    await apiClient.delete(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}`
    );
  },
};
