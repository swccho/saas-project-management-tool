import { defineStore } from 'pinia';
import { workspaceService } from '../services/workspaceService';

export const useWorkspaceStore = defineStore('workspace', {
  state: () => ({
    workspaces: [],
    activeWorkspaceId: null,
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
        this.activeWorkspaceId = this.workspaces[0].id;
      }
      return data;
    },

    setActive(id) {
      this.activeWorkspaceId = id;
    },

    async createWorkspace(name) {
      const workspace = await workspaceService.create(name);
      this.workspaces.push(workspace);
      this.activeWorkspaceId = workspace.id;
      return workspace;
    },
  },
});
