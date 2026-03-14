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
          <router-link to="/projects" class="block transition-colors hover:opacity-90">
            <Card class="h-full border-gray-200 transition-colors hover:border-indigo-200 hover:bg-indigo-50/30">
              <CardContent class="pt-6">
                <div class="flex items-center gap-3">
                  <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                    <FolderKanban class="h-5 w-5" />
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-500">Projects</p>
                    <p class="text-2xl font-semibold">{{ dashboard?.projects_count ?? 0 }}</p>
                  </div>
                </div>
              </CardContent>
            </Card>
          </router-link>
          <router-link to="/my-tasks" class="block transition-colors hover:opacity-90">
            <Card class="h-full border-gray-200 transition-colors hover:border-indigo-200 hover:bg-indigo-50/30">
              <CardContent class="pt-6">
                <div class="flex items-center gap-3">
                  <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                    <ListTodo class="h-5 w-5" />
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-500">My Tasks</p>
                    <p class="text-2xl font-semibold">{{ dashboard?.tasks_assigned ?? 0 }}</p>
                  </div>
                </div>
              </CardContent>
            </Card>
          </router-link>
          <router-link to="/my-tasks?view=overdue" class="block transition-colors hover:opacity-90">
            <Card class="h-full border-gray-200 transition-colors hover:border-indigo-200 hover:bg-indigo-50/30">
              <CardContent class="pt-6">
                <div class="flex items-center gap-3">
                  <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-red-100 text-red-600">
                    <AlertCircle class="h-5 w-5" />
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-500">Overdue</p>
                    <p class="text-2xl font-semibold" :class="(dashboard?.tasks_overdue ?? 0) > 0 && 'text-red-600'">
                      {{ dashboard?.tasks_overdue ?? 0 }}
                    </p>
                  </div>
                </div>
              </CardContent>
            </Card>
          </router-link>
          <router-link to="/my-tasks?view=due_week" class="block transition-colors hover:opacity-90">
            <Card class="h-full border-gray-200 transition-colors hover:border-indigo-200 hover:bg-indigo-50/30">
              <CardContent class="pt-6">
                <div class="flex items-center gap-3">
                  <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                    <CalendarClock class="h-5 w-5" />
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-500">Due Soon</p>
                    <p class="text-2xl font-semibold">{{ dashboard?.tasks_due_soon ?? 0 }}</p>
                  </div>
                </div>
              </CardContent>
            </Card>
          </router-link>
        </div>
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
import { AlertCircle, CalendarClock, FolderKanban, ListTodo } from 'lucide-vue-next';
import { useWorkspaceStore } from '../../stores/workspaceStore';
import { dashboardService } from '../../services/dashboardService';
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

const firstProjectId = computed(() => dashboard.value?.active_projects?.[0]?.id ?? null);

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

onMounted(() => fetchDashboard());
watch(() => workspaceStore.activeWorkspaceId, () => fetchDashboard());
</script>
