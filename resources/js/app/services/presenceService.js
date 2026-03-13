import apiClient from '../lib/apiClient';

const unwrap = (res) => res.data?.data ?? res.data;

let heartbeatInterval = null;
const HEARTBEAT_INTERVAL_MS = 60 * 1000;

export const presenceService = {
  async heartbeat() {
    const res = await apiClient.post('/presence/heartbeat');
    return unwrap(res);
  },

  async getWorkspacePresence(workspaceId) {
    const res = await apiClient.get(`/workspaces/${workspaceId}/presence`);
    return unwrap(res);
  },

  startHeartbeat() {
    this.stopHeartbeat();
    this.heartbeat();
    heartbeatInterval = setInterval(() => this.heartbeat(), HEARTBEAT_INTERVAL_MS);
  },

  stopHeartbeat() {
    if (heartbeatInterval) {
      clearInterval(heartbeatInterval);
      heartbeatInterval = null;
    }
  },
};
