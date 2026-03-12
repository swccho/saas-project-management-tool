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
    </div>
    <Card v-if="project">
      <CardHeader>
        <h2 class="text-lg font-semibold">Labels</h2>
        <p class="text-sm text-gray-600">Create and manage labels for this project.</p>
      </CardHeader>
      <CardContent class="space-y-4">
        <div class="flex flex-wrap gap-2">
          <span
            v-for="l in labels"
            :key="l.id"
            class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-sm"
            :style="{ backgroundColor: (l.color || '#6366F1') + '20', color: l.color || '#6366F1' }"
          >
            {{ l.name }}
          </span>
        </div>
        <form class="flex gap-2" @submit.prevent="createLabel">
          <input
            v-model="newLabelName"
            type="text"
            placeholder="New label name"
            class="rounded-lg border border-gray-300 px-3 py-2 text-sm"
          />
          <input
            v-model="newLabelColor"
            type="color"
            class="h-10 w-10 cursor-pointer rounded border-0"
          />
          <Button type="submit" :loading="creating">Add</Button>
        </form>
      </CardContent>
    </Card>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useWorkspaceStore } from '../../stores/workspaceStore';
import { projectService } from '../../services/projectService';
import { labelService } from '../../services/labelService';
import Card from '../../components/ui/Card.vue';
import CardHeader from '../../components/ui/CardHeader.vue';
import CardContent from '../../components/ui/CardContent.vue';
import Button from '../../components/ui/Button.vue';

const route = useRoute();
const workspaceStore = useWorkspaceStore();
const project = ref(null);
const labels = ref([]);
const newLabelName = ref('');
const newLabelColor = ref('#6366F1');
const creating = ref(false);

const projectId = route.params.id;

async function fetchProject() {
  const wid = workspaceStore.activeWorkspaceId;
  if (!wid || !projectId) return;
  try {
    project.value = await projectService.get(wid, projectId);
  } catch {
    project.value = null;
  }
}

async function fetchLabels() {
  const wid = workspaceStore.activeWorkspaceId;
  if (!wid || !projectId) return;
  try {
    const data = await labelService.list(wid, projectId);
    labels.value = Array.isArray(data) ? data : (data?.data ?? []);
  } catch {
    labels.value = [];
  }
}

async function createLabel() {
  const wid = workspaceStore.activeWorkspaceId;
  if (!wid || !projectId || !newLabelName.value.trim()) return;
  creating.value = true;
  try {
    await labelService.create(wid, projectId, {
      name: newLabelName.value.trim(),
      color: newLabelColor.value,
    });
    newLabelName.value = '';
    await fetchLabels();
  } finally {
    creating.value = false;
  }
}

watch([() => projectId, () => workspaceStore.activeWorkspaceId], () => {
  fetchProject();
  fetchLabels();
});

onMounted(async () => {
  if (workspaceStore.workspaces.length === 0) {
    await workspaceStore.fetchWorkspaces();
  }
  await fetchProject();
  await fetchLabels();
});
</script>
