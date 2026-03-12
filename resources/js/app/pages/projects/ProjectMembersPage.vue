<template>
  <div class="space-y-6">
    <div v-if="project" class="flex items-center gap-4">
      <router-link :to="`/projects/${projectId}`" class="text-sm text-gray-600 hover:text-gray-900">
        ← Back to Project
      </router-link>
    </div>
    <div v-if="project">
      <h1 class="text-2xl font-semibold tracking-tight">{{ project.name }}</h1>
      <p class="mt-1 text-sm text-gray-500">{{ project.key }}</p>
      <div class="mt-4 flex gap-2 border-b border-gray-200">
        <router-link
          :to="`/projects/${projectId}`"
          class="border-b-2 border-transparent px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700"
        >
          Overview
        </router-link>
        <span class="border-b-2 border-indigo-600 px-4 py-2 text-sm font-medium text-indigo-600">
          Members
        </span>
      </div>
    </div>
    <ProjectMembersSection
      v-if="workspaceStore.activeWorkspaceId && projectId"
      :workspace-id="workspaceStore.activeWorkspaceId"
      :project-id="Number(projectId)"
    />
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useWorkspaceStore } from '../../stores/workspaceStore';
import { projectService } from '../../services/projectService';
import ProjectMembersSection from '../../components/projects/ProjectMembersSection.vue';

const route = useRoute();
const workspaceStore = useWorkspaceStore();
const project = ref(null);

const projectId = computed(() => route.params.id);

async function fetchProject() {
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  if (!wid || !pid) return;
  try {
    project.value = await projectService.get(wid, pid);
  } catch {
    project.value = null;
  }
}

watch([projectId, () => workspaceStore.activeWorkspaceId], fetchProject);

onMounted(async () => {
  if (workspaceStore.workspaces.length === 0) {
    await workspaceStore.fetchWorkspaces();
  }
  await fetchProject();
});
</script>
