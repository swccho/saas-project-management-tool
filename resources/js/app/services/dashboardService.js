import apiClient from '../lib/apiClient';

const unwrap = (res) => res.data?.data ?? res.data;

export const dashboardService = {
  async getDashboard(workspaceId) {
    const res = await apiClient.get(`/workspaces/${workspaceId}/dashboard`);
    return unwrap(res);
  },
};
