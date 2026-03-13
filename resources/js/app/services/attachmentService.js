import apiClient from '../lib/apiClient';

const unwrap = (res) => res.data?.data ?? res.data;

export const attachmentService = {
  async list(workspaceId, projectId, boardId, taskId) {
    const res = await apiClient.get(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/tasks/${taskId}/attachments`
    );
    return unwrap(res);
  },

  async upload(workspaceId, projectId, boardId, taskId, file) {
    const formData = new FormData();
    formData.append('file', file);
    const res = await apiClient.post(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/tasks/${taskId}/attachments`,
      formData,
      {
        headers: { 'Content-Type': 'multipart/form-data' },
      }
    );
    return unwrap(res);
  },

  async delete(workspaceId, projectId, boardId, taskId, attachmentId) {
    await apiClient.delete(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/tasks/${taskId}/attachments/${attachmentId}`
    );
  },

  async downloadBlob(workspaceId, projectId, boardId, taskId, attachmentId) {
    const res = await apiClient.get(
      `/workspaces/${workspaceId}/projects/${projectId}/boards/${boardId}/tasks/${taskId}/attachments/${attachmentId}/download`,
      { responseType: 'blob' }
    );
    return res.data;
  },
};
