import apiClient from '../lib/apiClient';

const unwrap = (res) => res.data?.data ?? res.data;

export const projectMemberService = {
  async list(workspaceId, projectId) {
    const res = await apiClient.get(`/workspaces/${workspaceId}/projects/${projectId}/members`);
    return unwrap(res);
  },

  async add(workspaceId, projectId, payload) {
    const res = await apiClient.post(`/workspaces/${workspaceId}/projects/${projectId}/members`, payload);
    return unwrap(res);
  },

  async update(workspaceId, projectId, memberId, payload) {
    const res = await apiClient.put(
      `/workspaces/${workspaceId}/projects/${projectId}/members/${memberId}`,
      payload
    );
    return unwrap(res);
  },

  async remove(workspaceId, projectId, memberId) {
    await apiClient.delete(
      `/workspaces/${workspaceId}/projects/${projectId}/members/${memberId}`
    );
  },
};
