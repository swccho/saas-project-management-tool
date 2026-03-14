<template>
  <div class="space-y-6">
    <ActivityFeed
      :activities="activities"
      :loading="loading"
      :project-id="projectId"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import { useRoute } from 'vue-router';
import { useWorkspaceStore } from '../../stores/workspaceStore';
import { activityService } from '../../services/activityService';
import ActivityFeed from '../../components/shared/ActivityFeed.vue';

const route = useRoute();
const workspaceStore = useWorkspaceStore();
const activities = ref([]);
const loading = ref(false);

const projectId = computed(() => route.params.id);

async function fetchActivities() {
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  if (!wid || !pid) return;
  loading.value = true;
  try {
    const data = await activityService.listProjectActivities(wid, pid);
    activities.value = Array.isArray(data?.data) ? data.data : (data ?? []);
  } catch {
    activities.value = [];
  } finally {
    loading.value = false;
  }
}

onMounted(() => fetchActivities());
watch([() => workspaceStore.activeWorkspaceId, projectId], () => fetchActivities());
</script>
