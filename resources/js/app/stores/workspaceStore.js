import { defineStore } from 'pinia';
import { workspaceService } from '../services/workspaceService';

const ACTIVE_WORKSPACE_KEY = 'active_workspace_id';

export const useWorkspaceStore = defineStore('workspace', {
  state: () => ({
    workspaces: [],
    activeWorkspaceId: parseInt(localStorage.getItem(ACTIVE_WORKSPACE_KEY), 10) || null,
  }),

  getters: {
    activeWorkspace: (state) =>
      state.workspaces.find((w) => w.id === state.activeWorkspaceId) ?? null,
  },

  actions: {
    async fetchWorkspaces() {
      const data = await workspaceService.list();
      this.workspaces = Array.isArray(data) ? data : (data?.data ?? []);
      if (this.workspaces.length > 0 && !this.activeWorkspaceId) {
        this.setActive(this.workspaces[0].id);
      } else if (this.activeWorkspaceId && !this.workspaces.find((w) => w.id === this.activeWorkspaceId)) {
        this.setActive(this.workspaces[0]?.id ?? null);
      }
      return data;
    },

    setActive(id) {
      this.activeWorkspaceId = id;
      if (id) {
        localStorage.setItem(ACTIVE_WORKSPACE_KEY, String(id));
      } else {
        localStorage.removeItem(ACTIVE_WORKSPACE_KEY);
      }
    },

    async createWorkspace(name) {
      const workspace = await workspaceService.create(name);
      this.workspaces.push(workspace);
      this.setActive(workspace.id);
      return workspace;
    },

    async updateWorkspace(id, payload) {
      const workspace = await workspaceService.update(id, payload);
      const idx = this.workspaces.findIndex((w) => w.id === id);
      if (idx >= 0) this.workspaces[idx] = workspace;
      return workspace;
    },

    async deleteWorkspace(id) {
      await workspaceService.delete(id);
      this.workspaces = this.workspaces.filter((w) => w.id !== id);
      if (this.activeWorkspaceId === id) {
        this.setActive(this.workspaces[0]?.id ?? null);
      }
    },
  },
});
