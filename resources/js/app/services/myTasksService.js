import apiClient from '../lib/apiClient';

const unwrap = (res) => res.data?.data ?? res.data;

export const myTasksService = {
  async list(workspaceId, params = {}) {
    const res = await apiClient.get(`/workspaces/${workspaceId}/my-tasks`, { params });
    return unwrap(res);
  },
};
