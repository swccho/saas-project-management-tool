import apiClient from '../lib/apiClient';

const unwrap = (res) => res.data?.data ?? res.data;

export const invitationService = {
  async preview(token) {
    const res = await apiClient.get(`/invitations/${token}`);
    return unwrap(res);
  },

  async list(workspaceId) {
    const res = await apiClient.get(`/workspaces/${workspaceId}/invitations`);
    return unwrap(res);
  },

  async create(workspaceId, payload) {
    const res = await apiClient.post(`/workspaces/${workspaceId}/invitations`, payload);
    return unwrap(res);
  },

  async resend(workspaceId, invitationId) {
    const res = await apiClient.post(`/workspaces/${workspaceId}/invitations/${invitationId}/resend`);
    return unwrap(res);
  },

  async revoke(workspaceId, invitationId) {
    await apiClient.delete(`/workspaces/${workspaceId}/invitations/${invitationId}`);
  },

  async accept(token) {
    const res = await apiClient.post(`/invitations/${token}/accept`);
    return unwrap(res);
  },

  async reject(token) {
    const res = await apiClient.post(`/invitations/${token}/reject`);
    return unwrap(res);
  },
};
