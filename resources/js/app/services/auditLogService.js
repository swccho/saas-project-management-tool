import apiClient from '../lib/apiClient';

const unwrap = (res) => res.data?.data ?? res.data;

export const auditLogService = {
  async list(workspaceId, params = {}) {
    const qs = new URLSearchParams(params).toString();
    const url = `/workspaces/${workspaceId}/audit-logs${qs ? `?${qs}` : ''}`;
    const res = await apiClient.get(url);
    const data = unwrap(res);
    return {
      data: data?.data ?? data,
      meta: data?.meta ?? { current_page: 1, last_page: 1, total: 0 },
    };
  },
};
