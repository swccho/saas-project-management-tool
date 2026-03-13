<template>
  <Card>
    <CardHeader>
      <h2 class="text-lg font-semibold">Preferences</h2>
      <p class="text-sm text-gray-600">Configure workspace defaults and behavior.</p>
    </CardHeader>
    <CardContent>
      <div v-if="loading" class="space-y-4">
        <div class="h-10 animate-pulse rounded bg-gray-100" />
        <div class="h-10 animate-pulse rounded bg-gray-100" />
      </div>
      <form v-else class="space-y-6" @submit.prevent="save">
        <div class="space-y-4">
          <h3 class="text-sm font-medium text-gray-700">Display</h3>
          <div class="space-y-1">
            <label class="block text-sm font-medium text-gray-700">Date format</label>
            <Select
              v-model="form.date_format"
              :options="dateFormatOptions"
              placeholder="Select format"
            />
          </div>
          <div class="space-y-1">
            <label class="block text-sm font-medium text-gray-700">Time format</label>
            <Select
              v-model="form.time_format"
              :options="timeFormatOptions"
              placeholder="Select format"
            />
          </div>
        </div>
        <div class="space-y-4">
          <h3 class="text-sm font-medium text-gray-700">Projects</h3>
          <div class="space-y-1">
            <label class="block text-sm font-medium text-gray-700">Default project view</label>
            <Select
              v-model="form.default_project_view"
              :options="projectViewOptions"
              placeholder="Select view"
            />
          </div>
        </div>
        <div class="space-y-4">
          <h3 class="text-sm font-medium text-gray-700">Tasks</h3>
          <div class="space-y-1">
            <label class="block text-sm font-medium text-gray-700">Task number format</label>
            <Select
              v-model="form.task_number_format"
              :options="taskNumberOptions"
              placeholder="Select format"
            />
            <p class="text-xs text-gray-500">Numeric: 1, 2, 3. Key: PROJ-1, PROJ-2.</p>
          </div>
        </div>
        <div class="flex gap-2">
          <Button :loading="saving" type="submit">Save preferences</Button>
          <Button variant="secondary" type="button" @click="resetForm">Reset</Button>
        </div>
        <p v-if="saveSuccess" class="text-sm text-green-600">Saved successfully.</p>
        <p v-if="saveError" class="text-sm text-red-500">{{ saveError }}</p>
      </form>
    </CardContent>
  </Card>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue';
import { useWorkspaceStore } from '../../../stores/workspaceStore';
import { workspaceService } from '../../../services/workspaceService';
import Card from '../../../components/ui/Card.vue';
import CardHeader from '../../../components/ui/CardHeader.vue';
import CardContent from '../../../components/ui/CardContent.vue';
import Select from '../../../components/ui/Select.vue';
import Button from '../../../components/ui/Button.vue';

const workspaceStore = useWorkspaceStore();

const form = reactive({
  date_format: 'Y-m-d',
  time_format: '24h',
  default_project_view: 'list',
  task_number_format: 'key',
});

const loading = ref(false);
const saving = ref(false);
const saveSuccess = ref('');
const saveError = ref('');

const dateFormatOptions = [
  { value: 'Y-m-d', label: 'YYYY-MM-DD (2025-03-13)' },
  { value: 'm/d/Y', label: 'MM/DD/YYYY (03/13/2025)' },
  { value: 'd/m/Y', label: 'DD/MM/YYYY (13/03/2025)' },
];

const timeFormatOptions = [
  { value: '24h', label: '24-hour' },
  { value: '12h', label: '12-hour' },
];

const projectViewOptions = [
  { value: 'list', label: 'List' },
  { value: 'grid', label: 'Grid' },
];

const taskNumberOptions = [
  { value: 'key', label: 'Project key (PROJ-1)' },
  { value: 'numeric', label: 'Numeric (1, 2, 3)' },
];

async function fetchPreferences() {
  const ws = workspaceStore.activeWorkspace;
  if (!ws) return;
  loading.value = true;
  try {
    const prefs = await workspaceService.getPreferences(ws.id) ?? {};
    form.date_format = prefs.date_format ?? 'Y-m-d';
    form.time_format = prefs.time_format ?? '24h';
    form.default_project_view = prefs.default_project_view ?? 'list';
    form.task_number_format = prefs.task_number_format ?? 'key';
  } finally {
    loading.value = false;
  }
}

function resetForm() {
  fetchPreferences();
}

async function save() {
  const ws = workspaceStore.activeWorkspace;
  if (!ws) return;
  saving.value = true;
  saveError.value = '';
  saveSuccess.value = '';
  try {
    await workspaceService.updatePreferences(ws.id, {
      date_format: form.date_format,
      time_format: form.time_format,
      default_project_view: form.default_project_view,
      task_number_format: form.task_number_format,
    });
    saveSuccess.value = 'Saved successfully.';
  } catch (e) {
    saveError.value = e.response?.data?.message ?? 'Failed to save.';
  } finally {
    saving.value = false;
  }
}

onMounted(fetchPreferences);
watch(() => workspaceStore.activeWorkspaceId, fetchPreferences);
</script>
