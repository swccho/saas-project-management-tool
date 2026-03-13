import apiClient from '../lib/apiClient';

const unwrap = (res) => res.data?.data ?? res.data;

export const activityService = {
  async listWorkspaceActivities(workspaceId, params = {}) {
    const res = await apiClient.get(`/workspaces/${workspaceId}/activities`, { params });
    return unwrap(res);
  },

  async listProjectActivities(workspaceId, projectId, params = {}) {
    const res = await apiClient.get(
      `/workspaces/${workspaceId}/projects/${projectId}/activities`,
      { params }
    );
    return unwrap(res);
  },
};
