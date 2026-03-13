import apiClient from '../lib/apiClient';

const unwrap = (res) => res.data?.data ?? res.data;

export const analyticsService = {
  async getWorkspaceAnalytics(workspaceId) {
    const res = await apiClient.get(`/workspaces/${workspaceId}/analytics`);
    return unwrap(res);
  },

  async getProjectAnalytics(workspaceId, projectId) {
    const res = await apiClient.get(
      `/workspaces/${workspaceId}/projects/${projectId}/analytics`
    );
    return unwrap(res);
  },
};
