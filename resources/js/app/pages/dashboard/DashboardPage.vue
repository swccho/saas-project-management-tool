<template>
  <div class="space-y-6">
    <h1 class="text-2xl font-semibold tracking-tight">Dashboard</h1>

    <EmptyState
      v-if="!workspaceStore.activeWorkspaceId"
      title="Select a workspace"
      description="Choose a workspace from the sidebar to view your dashboard."
    />

    <template v-else>
      <div v-if="loading" class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <div v-for="i in 4" :key="i" class="h-24 animate-pulse rounded-lg bg-gray-100" />
      </div>
      <div v-else class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
          <Card>
            <CardContent class="pt-6">
              <p class="text-sm font-medium text-gray-500">Projects</p>
              <p class="text-2xl font-semibold">{{ dashboard?.projects_count ?? 0 }}</p>
            </CardContent>
          </Card>
          <Card>
            <CardContent class="pt-6">
              <p class="text-sm font-medium text-gray-500">My Tasks</p>
              <p class="text-2xl font-semibold">{{ dashboard?.tasks_assigned ?? 0 }}</p>
            </CardContent>
          </Card>
          <Card>
            <CardContent class="pt-6">
              <p class="text-sm font-medium text-gray-500">Overdue</p>
              <p class="text-2xl font-semibold" :class="(dashboard?.tasks_overdue ?? 0) > 0 && 'text-red-600'">
                {{ dashboard?.tasks_overdue ?? 0 }}
              </p>
            </CardContent>
          </Card>
          <Card>
            <CardContent class="pt-6">
              <p class="text-sm font-medium text-gray-500">Due Soon</p>
              <p class="text-2xl font-semibold">{{ dashboard?.tasks_due_soon ?? 0 }}</p>
            </CardContent>
          </Card>
        </div>
        <Card v-if="workspaceAnalytics">
          <CardHeader>
            <h2 class="text-lg font-semibold">Workspace Analytics</h2>
          </CardHeader>
          <CardContent>
            <AnalyticsCards :analytics="workspaceAnalytics" :loading="analyticsLoading" />
          </CardContent>
        </Card>
      </div>

      <div class="grid gap-6 lg:grid-cols-2">
        <Card>
          <CardHeader>
            <h2 class="text-lg font-semibold">Recent Activity</h2>
          </CardHeader>
          <CardContent>
            <RecentActivityWidget
              :activities="dashboard?.recent_activity ?? []"
              :loading="loading"
            />
          </CardContent>
        </Card>
        <div class="space-y-6">
          <Card v-if="(dashboard?.favorite_projects ?? []).length > 0">
            <CardHeader>
              <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold">Favorites</h2>
                <router-link
                  to="/projects"
                  class="text-sm font-medium text-indigo-600 hover:underline"
                >
                  View all
                </router-link>
              </div>
            </CardHeader>
            <CardContent>
              <div class="space-y-2">
                <router-link
                  v-for="p in dashboard?.favorite_projects"
                  :key="p.id"
                  :to="`/projects/${p.id}`"
                  class="flex items-center gap-3 rounded-lg border border-gray-100 px-3 py-2 text-sm hover:bg-gray-50"
                >
                  <span
                    v-if="p.color"
                    class="h-2 w-2 shrink-0 rounded-full"
                    :style="{ backgroundColor: p.color }"
                  />
                  <span class="font-medium text-gray-900">{{ p.name }}</span>
                  <span class="text-xs text-gray-500">{{ p.key }}</span>
                </router-link>
              </div>
            </CardContent>
          </Card>
          <Card>
            <CardHeader>
              <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold">Active Projects</h2>
                <router-link
                  to="/projects"
                  class="text-sm font-medium text-indigo-600 hover:underline"
                >
                  View all
                </router-link>
              </div>
            </CardHeader>
            <CardContent>
              <div v-if="loading" class="space-y-2">
                <div v-for="i in 4" :key="i" class="h-10 animate-pulse rounded bg-gray-100" />
              </div>
              <EmptyState
                v-else-if="(dashboard?.active_projects ?? []).length === 0"
                title="No projects yet"
                description="Create your first project to get started."
                compact
              >
                <router-link
                  to="/projects"
                  class="text-sm font-medium text-indigo-600 hover:underline"
                >
                  Create project
                </router-link>
              </EmptyState>
              <div v-else class="space-y-2">
                <router-link
                  v-for="p in dashboard?.active_projects"
                  :key="p.id"
                  :to="`/projects/${p.id}`"
                  class="flex items-center gap-3 rounded-lg border border-gray-100 px-3 py-2 text-sm hover:bg-gray-50"
                >
                  <span
                    v-if="p.color"
                    class="h-2 w-2 shrink-0 rounded-full"
                    :style="{ backgroundColor: p.color }"
                  />
                  <span class="font-medium text-gray-900">{{ p.name }}</span>
                  <span class="text-xs text-gray-500">{{ p.key }}</span>
                </router-link>
              </div>
            </CardContent>
          </Card>
          <Card>
            <CardContent class="pt-6">
              <QuickActionsPanel :project-id="firstProjectId" />
            </CardContent>
          </Card>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import { useWorkspaceStore } from '../../stores/workspaceStore';
import { dashboardService } from '../../services/dashboardService';
import { analyticsService } from '../../services/analyticsService';
import Card from '../../components/ui/Card.vue';
import CardHeader from '../../components/ui/CardHeader.vue';
import CardContent from '../../components/ui/CardContent.vue';
import RecentActivityWidget from '../../components/dashboard/RecentActivityWidget.vue';
import QuickActionsPanel from '../../components/dashboard/QuickActionsPanel.vue';
import AnalyticsCards from '../../components/dashboard/AnalyticsCards.vue';
import EmptyState from '../../components/shared/EmptyState.vue';

const workspaceStore = useWorkspaceStore();
const dashboard = ref(null);
const loading = ref(false);
const workspaceAnalytics = ref(null);
const analyticsLoading = ref(false);

const firstProjectId = computed(() => dashboard.value?.active_projects?.[0]?.id ?? null);

async function fetchAnalytics() {
  const wid = workspaceStore.activeWorkspaceId;
  if (!wid) return;
  analyticsLoading.value = true;
  try {
    workspaceAnalytics.value = await analyticsService.getWorkspaceAnalytics(wid);
  } catch {
    workspaceAnalytics.value = null;
  } finally {
    analyticsLoading.value = false;
  }
}

async function fetchDashboard() {
  const wid = workspaceStore.activeWorkspaceId;
  if (!wid) {
    dashboard.value = null;
    return;
  }
  loading.value = true;
  try {
    dashboard.value = await dashboardService.getDashboard(wid);
  } catch {
    dashboard.value = null;
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  fetchDashboard();
  fetchAnalytics();
});
watch(() => workspaceStore.activeWorkspaceId, () => {
  fetchDashboard();
  fetchAnalytics();
});
</script>
