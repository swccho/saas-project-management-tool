<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold tracking-tight">Projects</h1>
      <Button v-if="workspaceStore.activeWorkspace" @click="showCreate = true">
        New project
      </Button>
    </div>

    <template v-if="!workspaceStore.activeWorkspace">
      <Card>
        <CardContent class="py-8 text-center">
          <p class="text-sm text-gray-600">Select or create a workspace to manage projects.</p>
        </CardContent>
      </Card>
    </template>

    <template v-else-if="loading">
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div v-for="i in 6" :key="i" class="h-32 animate-pulse rounded-xl bg-gray-100" />
      </div>
    </template>

    <template v-else-if="projects.length === 0">
      <Card>
        <CardContent class="py-12 text-center">
          <h3 class="text-lg font-medium text-gray-900">No projects yet</h3>
          <p class="mt-1 text-sm text-gray-600">Create your first project to get started.</p>
          <Button class="mt-4" @click="showCreate = true">Create project</Button>
        </CardContent>
      </Card>
    </template>

    <template v-else>
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <ProjectCard v-for="p in projects" :key="p.id" :project="p" />
      </div>
    </template>

    <CreateProjectModal
      v-model="showCreate"
      :loading="creating"
      @submit="handleCreate"
    />
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { useWorkspaceStore } from '../../stores/workspaceStore';
import { projectService } from '../../services/projectService';
import Button from '../../components/ui/Button.vue';
import Card from '../../components/ui/Card.vue';
import CardContent from '../../components/ui/CardContent.vue';
import ProjectCard from '../../components/projects/ProjectCard.vue';
import CreateProjectModal from '../../components/projects/CreateProjectModal.vue';

const workspaceStore = useWorkspaceStore();
const projects = ref([]);
const loading = ref(false);
const showCreate = ref(false);
const creating = ref(false);

async function fetchProjects() {
  const wid = workspaceStore.activeWorkspaceId;
  if (!wid) {
    projects.value = [];
    return;
  }
  loading.value = true;
  try {
    const data = await projectService.list(wid);
    projects.value = Array.isArray(data) ? data : (data?.data ?? []);
  } finally {
    loading.value = false;
  }
}

async function handleCreate(formData) {
  const wid = workspaceStore.activeWorkspaceId;
  if (!wid) return;
  creating.value = true;
  try {
    await projectService.create(wid, formData);
    showCreate.value = false;
    await fetchProjects();
  } finally {
    creating.value = false;
  }
}

watch(() => workspaceStore.activeWorkspaceId, fetchProjects, { immediate: false });

onMounted(async () => {
  if (workspaceStore.workspaces.length === 0) {
    await workspaceStore.fetchWorkspaces();
  }
  await fetchProjects();
});
</script>
