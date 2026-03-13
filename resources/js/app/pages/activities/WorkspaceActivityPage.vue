<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-semibold text-gray-900">Workspace Activity</h1>
      <router-link
        :to="`/`"
        class="text-sm text-indigo-600 hover:underline"
      >
        ← Dashboard
      </router-link>
    </div>
    <ActivityFeed
      :activities="activities"
      :loading="loading"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useWorkspaceStore } from '../../stores/workspaceStore';
import { activityService } from '../../services/activityService';
import ActivityFeed from '../../components/shared/ActivityFeed.vue';

const workspaceStore = useWorkspaceStore();
const activities = ref([]);
const loading = ref(false);

async function fetchActivities() {
  const wid = workspaceStore.activeWorkspaceId;
  if (!wid) return;
  loading.value = true;
  try {
    const data = await activityService.listWorkspaceActivities(wid);
    activities.value = Array.isArray(data?.data) ? data.data : (data ?? []);
  } catch {
    activities.value = [];
  } finally {
    loading.value = false;
  }
}

onMounted(() => fetchActivities());
watch(() => workspaceStore.activeWorkspaceId, () => fetchActivities());
</script>
