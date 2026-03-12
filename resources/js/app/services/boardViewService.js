import apiClient from '../lib/apiClient';

const unwrap = (res) => res.data?.data ?? res.data;

export const boardViewService = {
  async list(workspaceId, projectId, boardId) {
    const res = await apiClient.get(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/views`
    );
    return unwrap(res);
  },

  async create(workspaceId, projectId, boardId, payload) {
    const res = await apiClient.post(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/views`,
      payload
    );
    return unwrap(res);
  },

  async update(workspaceId, projectId, boardId, viewId, payload) {
    const res = await apiClient.put(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/views/${viewId}`,
      payload
    );
    return unwrap(res);
  },

  async delete(workspaceId, projectId, boardId, viewId) {
    await apiClient.delete(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/views/${viewId}`
    );
  },
};
