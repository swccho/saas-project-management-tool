import apiClient from '../lib/apiClient';

const unwrap = (res) => res.data?.data ?? res.data;

export const subtaskService = {
  async list(workspaceId, projectId, boardId, taskId) {
    const res = await apiClient.get(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/tasks/${taskId}/subtasks`
    );
    return unwrap(res);
  },

  async create(workspaceId, projectId, boardId, taskId, payload) {
    const res = await apiClient.post(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/tasks/${taskId}/subtasks`,
      payload
    );
    return unwrap(res);
  },

  async update(workspaceId, projectId, boardId, taskId, subtaskId, payload) {
    const res = await apiClient.put(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/tasks/${taskId}/subtasks/${subtaskId}`,
      payload
    );
    return unwrap(res);
  },

  async delete(workspaceId, projectId, boardId, taskId, subtaskId) {
    await apiClient.delete(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/tasks/${taskId}/subtasks/${subtaskId}`
    );
  },
};
