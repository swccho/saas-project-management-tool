import apiClient from '../lib/apiClient';

const unwrap = (res) => res.data?.data ?? res.data;

export const commentService = {
  async list(workspaceId, projectId, boardId, taskId) {
    const res = await apiClient.get(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/tasks/${taskId}/comments`
    );
    return unwrap(res);
  },

  async create(workspaceId, projectId, boardId, taskId, payload) {
    const res = await apiClient.post(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/tasks/${taskId}/comments`,
      payload
    );
    return unwrap(res);
  },

  async update(workspaceId, projectId, boardId, taskId, commentId, payload) {
    const res = await apiClient.put(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/tasks/${taskId}/comments/${commentId}`,
      payload
    );
    return unwrap(res);
  },

  async delete(workspaceId, projectId, boardId, taskId, commentId) {
    await apiClient.delete(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/tasks/${taskId}/comments/${commentId}`
    );
  },
};
