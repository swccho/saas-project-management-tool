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
        <CardContent class="p-0">
          <EmptyState
            title="No workspace selected"
            description="Select or create a workspace to manage projects."
            :icon="Building2"
          />
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
        <CardContent class="p-0">
          <EmptyState
            title="No projects yet"
            description="Create your first project to get started."
          >
            <Button @click="showCreate = true">Create project</Button>
          </EmptyState>
        </CardContent>
      </Card>
    </template>

    <template v-else>
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <ProjectCard v-for="p in projects" :key="p.id" :project="p" @favorite-changed="fetchProjects" />
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
import { Building2 } from 'lucide-vue-next';
import { useWorkspaceStore } from '../../stores/workspaceStore';
import { projectService } from '../../services/projectService';
import { showSuccessToast } from '../../services/apiErrorHandler';
import Button from '../../components/ui/Button.vue';
import Card from '../../components/ui/Card.vue';
import CardContent from '../../components/ui/CardContent.vue';
import ProjectCard from '../../components/projects/ProjectCard.vue';
import CreateProjectModal from '../../components/projects/CreateProjectModal.vue';
import EmptyState from '../../components/shared/EmptyState.vue';

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
    showSuccessToast('Project created successfully');
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
