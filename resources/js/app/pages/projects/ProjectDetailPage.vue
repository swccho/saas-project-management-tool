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
          :to="`/projects/${project.id}/settings`"
          class="border-b-2 border-transparent px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700"
        >
          Settings
        </router-link>
      </div>
      <Card>
        <CardContent class="py-8">
          <p class="text-sm text-gray-500">Project overview. Board and settings coming in next steps.</p>
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
import { useWorkspaceStore } from '../../stores/workspaceStore';
import { projectService } from '../../services/projectService';
import Card from '../../components/ui/Card.vue';
import CardContent from '../../components/ui/CardContent.vue';

const route = useRoute();
const workspaceStore = useWorkspaceStore();
const project = ref(null);
const loading = ref(false);

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

watch([() => route.params.id, () => workspaceStore.activeWorkspaceId], fetchProject);

onMounted(async () => {
  if (workspaceStore.workspaces.length === 0) {
    await workspaceStore.fetchWorkspaces();
  }
  await fetchProject();
});
</script>
