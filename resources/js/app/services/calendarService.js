import apiClient from '../lib/apiClient';

const unwrap = (res) => res.data?.data ?? res.data;

export const calendarService = {
  async getTasks(workspaceId, start, end) {
    const res = await apiClient.get(`/workspaces/${workspaceId}/calendar`, {
      params: { start, end },
    });
    return unwrap(res);
  },
};
