import apiClient from '../lib/apiClient';

const unwrap = (res) => res.data?.data ?? res.data;

export const boardColumnService = {
  async list(workspaceId, projectId, boardId) {
    const res = await apiClient.get(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/columns`
    );
    return unwrap(res);
  },

  async create(workspaceId, projectId, boardId, payload) {
    const res = await apiClient.post(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/columns`,
      payload
    );
    return unwrap(res);
  },

  async update(workspaceId, projectId, boardId, columnId, payload) {
    const res = await apiClient.put(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/columns/${columnId}`,
      payload
    );
    return unwrap(res);
  },

  async delete(workspaceId, projectId, boardId, columnId) {
    await apiClient.delete(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/columns/${columnId}`
    );
  },

  async reorder(workspaceId, projectId, boardId, columnIds) {
    const res = await apiClient.post(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/columns/reorder`,
      { column_ids: columnIds }
    );
    return unwrap(res);
  },
};
