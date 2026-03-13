<template>
  <div class="space-y-6">
    <div v-if="loading" class="animate-pulse space-y-4">
      <div class="h-8 w-48 bg-gray-100 rounded" />
      <div class="h-4 w-96 bg-gray-100 rounded" />
    </div>
    <template v-else-if="project">
      <div class="flex items-center gap-4">
        <router-link to="/projects" class="text-sm text-gray-600 hover:text-gray-900">
          ← Back to Projects
        </router-link>
      </div>
      <div class="flex items-start justify-between gap-4">
        <div>
          <h1 class="text-2xl font-semibold tracking-tight">{{ project.name }}</h1>
          <p class="mt-1 text-sm text-gray-500">{{ project.key }}</p>
          <p v-if="project.description" class="mt-2 text-sm text-gray-600">
            {{ project.description }}
          </p>
        </div>
        <button
          type="button"
          class="rounded p-2 text-gray-400 hover:bg-gray-100 hover:text-amber-500"
          :class="{ 'text-amber-500': project.is_favorite }"
          :aria-label="project.is_favorite ? 'Remove from favorites' : 'Add to favorites'"
          @click="toggleFavorite"
        >
          <Star :class="['h-5 w-5', project.is_favorite && 'fill-current']" />
        </button>
      </div>
      <div class="flex gap-2 border-b border-gray-200">
        <router-link
          :to="`/projects/${project.id}`"
          class="border-b-2 border-indigo-600 px-4 py-2 text-sm font-medium text-indigo-600"
        >
          Overview
        </router-link>
        <router-link
          :to="`/projects/${project.id}/members`"
          class="border-b-2 border-transparent px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700"
        >
          Members
        </router-link>
        <router-link
          :to="`/projects/${project.id}/board`"
          class="border-b-2 border-transparent px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700"
        >
          Board
        </router-link>
        <router-link
          :to="`/projects/${project.id}/activity`"
          class="border-b-2 border-transparent px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700"
        >
          Activity
        </router-link>
        <router-link
          :to="`/projects/${project.id}/settings`"
          class="border-b-2 border-transparent px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700"
        >
          Settings
        </router-link>
      </div>
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
    </template>
    <template v-else>
      <Card>
        <CardContent class="py-8 text-center">
          <p class="text-sm text-gray-600">Project not found.</p>
          <router-link to="/projects" class="mt-4 inline-block text-sm font-medium text-indigo-600">
            Back to Projects
          </router-link>
        </CardContent>
      </Card>
    </template>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { Star } from 'lucide-vue-next';
import { useWorkspaceStore } from '../../stores/workspaceStore';
import { projectService } from '../../services/projectService';
import { analyticsService } from '../../services/analyticsService';
import Card from '../../components/ui/Card.vue';
import CardHeader from '../../components/ui/CardHeader.vue';
import CardContent from '../../components/ui/CardContent.vue';
import AnalyticsCards from '../../components/dashboard/AnalyticsCards.vue';

const route = useRoute();
const workspaceStore = useWorkspaceStore();
const project = ref(null);
const loading = ref(false);
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

async function fetchProject() {
  const wid = workspaceStore.activeWorkspaceId;
  const pid = route.params.id;
  if (!wid || !pid) {
    project.value = null;
    return;
  }
  loading.value = true;
  try {
    project.value = await projectService.get(wid, pid);
  } catch {
    project.value = null;
  } finally {
    loading.value = false;
  }
}

async function toggleFavorite() {
  const wid = workspaceStore.activeWorkspaceId;
  if (!wid || !project.value) return;
  const prev = project.value.is_favorite;
  project.value = { ...project.value, is_favorite: !prev };
  try {
    if (prev) {
      await projectService.unfavorite(wid, project.value.id);
    } else {
      await projectService.favorite(wid, project.value.id);
    }
    window.dispatchEvent(new CustomEvent('favorites-changed'));
  } catch {
    project.value = { ...project.value, is_favorite: prev };
  }
}

watch([() => route.params.id, () => workspaceStore.activeWorkspaceId], () => {
  fetchProject();
  fetchAnalytics();
});

onMounted(async () => {
  if (workspaceStore.workspaces.length === 0) {
    await workspaceStore.fetchWorkspaces();
  }
  await fetchProject();
  await fetchAnalytics();
});
</script>
