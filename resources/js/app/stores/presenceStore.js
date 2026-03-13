import { defineStore } from 'pinia';
import { ref } from 'vue';
import { presenceService } from '../services/presenceService';
import { useWorkspaceStore } from './workspaceStore';

export const usePresenceStore = defineStore('presence', () => {
  const presenceMap = ref({});
  const loading = ref(false);

  async function fetchPresence() {
    const workspaceStore = useWorkspaceStore();
    const wid = workspaceStore?.activeWorkspaceId;
    if (!wid) {
      presenceMap.value = {};
      return;
    }
    loading.value = true;
    try {
      const data = await presenceService.getWorkspacePresence(wid);
      const map = {};
      (Array.isArray(data) ? data : []).forEach((p) => {
        map[p.user_id] = p.status;
      });
      presenceMap.value = map;
    } catch {
      presenceMap.value = {};
    } finally {
      loading.value = false;
    }
  }

  function getStatus(userId) {
    return presenceMap.value[userId] ?? 'offline';
  }

  return {
    presenceMap,
    loading,
    fetchPresence,
    getStatus,
  };
});
