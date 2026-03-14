<template>
  <div class="space-y-6">
    <Card>
      <CardHeader>
        <h2 class="text-lg font-semibold">Project Analytics</h2>
      </CardHeader>
      <CardContent>
        <AnalyticsCards
          :analytics="projectAnalytics"
          :loading="analyticsLoading"
        />
      </CardContent>
    </Card>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useWorkspaceStore } from '../../stores/workspaceStore';
import { analyticsService } from '../../services/analyticsService';
import Card from '../../components/ui/Card.vue';
import CardHeader from '../../components/ui/CardHeader.vue';
import CardContent from '../../components/ui/CardContent.vue';
import AnalyticsCards from '../../components/dashboard/AnalyticsCards.vue';

const route = useRoute();
const workspaceStore = useWorkspaceStore();
const projectAnalytics = ref(null);
const analyticsLoading = ref(false);

async function fetchAnalytics() {
  const wid = workspaceStore.activeWorkspaceId;
  const pid = route.params.id;
  if (!wid || !pid) return;
  analyticsLoading.value = true;
  try {
    projectAnalytics.value = await analyticsService.getProjectAnalytics(wid, pid);
  } catch {
    projectAnalytics.value = null;
  } finally {
    analyticsLoading.value = false;
  }
}

watch([() => route.params.id, () => workspaceStore.activeWorkspaceId], () => {
  fetchAnalytics();
});

onMounted(async () => {
  if (workspaceStore.workspaces.length === 0) {
    await workspaceStore.fetchWorkspaces();
  }
  await fetchAnalytics();
});
</script>
