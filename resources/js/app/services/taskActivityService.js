import apiClient from '../lib/apiClient';

const unwrap = (res) => res.data?.data ?? res.data;

export const taskActivityService = {
  async list(workspaceId, projectId, boardId, taskId) {
    const res = await apiClient.get(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/tasks/${taskId}/activities`
    );
    return unwrap(res);
  },
};
