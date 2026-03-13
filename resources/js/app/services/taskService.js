import apiClient from '../lib/apiClient';

const unwrap = (res) => res.data?.data ?? res.data;

export const taskService = {
  async list(workspaceId, projectId, boardId) {
    const res = await apiClient.get(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/tasks`
    );
    return unwrap(res);
  },

  async get(workspaceId, projectId, boardId, taskId) {
    const res = await apiClient.get(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/tasks/${taskId}`
    );
    return unwrap(res);
  },

  async create(workspaceId, projectId, boardId, payload) {
    const res = await apiClient.post(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/tasks`,
      payload
    );
    return unwrap(res);
  },

  async update(workspaceId, projectId, boardId, taskId, payload) {
    const res = await apiClient.put(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/tasks/${taskId}`,
      payload
    );
    return unwrap(res);
  },

  async delete(workspaceId, projectId, boardId, taskId) {
    await apiClient.delete(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/tasks/${taskId}`
    );
  },

  async move(workspaceId, projectId, boardId, taskId, columnId, sortOrder) {
    const res = await apiClient.post(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/tasks/${taskId}/move`,
      { column_id: columnId, sort_order: sortOrder }
    );
    return unwrap(res);
  },

  async setAssignee(workspaceId, projectId, boardId, taskId, userId) {
    const res = await apiClient.put(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/tasks/${taskId}/assignee`,
      { user_id: userId }
    );
    return unwrap(res);
  },

  async updateMeta(workspaceId, projectId, boardId, taskId, payload) {
    const res = await apiClient.put(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/tasks/${taskId}/meta`,
      payload
    );
    return unwrap(res);
  },

  async setLabels(workspaceId, projectId, boardId, taskId, labelIds) {
    const res = await apiClient.put(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/tasks/${taskId}/labels`,
      { label_ids: labelIds }
    );
    return unwrap(res);
  },

  async watch(workspaceId, projectId, boardId, taskId) {
    const res = await apiClient.post(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/tasks/${taskId}/watch`
    );
    return unwrap(res);
  },

  async unwatch(workspaceId, projectId, boardId, taskId) {
    const res = await apiClient.delete(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/tasks/${taskId}/watch`
    );
    return unwrap(res);
  },
};
