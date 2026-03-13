<template>
  <Card>
    <CardHeader>
      <h2 class="text-lg font-semibold">General</h2>
      <p class="text-sm text-gray-600">Update your workspace name, description, and URL slug.</p>
    </CardHeader>
    <CardContent class="space-y-4">
      <Input
        v-model="form.name"
        label="Name"
        placeholder="My Workspace"
      />
      <div class="space-y-1">
        <label class="block text-sm font-medium text-gray-700">Description</label>
        <textarea
          v-model="form.description"
          placeholder="Brief description of your workspace"
          rows="3"
          class="flex w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
        />
      </div>
      <Input
        v-model="form.slug"
        label="URL slug"
        placeholder="my-workspace"
      />
      <p class="text-xs text-gray-500">Used in URLs. Lowercase letters, numbers, and hyphens only.</p>
      <div class="flex gap-2">
        <Button :loading="saving" @click="save">Save changes</Button>
        <Button variant="secondary" @click="resetForm">Reset</Button>
      </div>
      <p v-if="saveError" class="text-sm text-red-500">{{ saveError }}</p>
      <p v-if="saveSuccess" class="text-sm text-green-600">Saved successfully.</p>
    </CardContent>
  </Card>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useWorkspaceStore } from '../../../stores/workspaceStore';
import Card from '../../../components/ui/Card.vue';
import CardHeader from '../../../components/ui/CardHeader.vue';
import CardContent from '../../../components/ui/CardContent.vue';
import Input from '../../../components/ui/Input.vue';
import Button from '../../../components/ui/Button.vue';

const workspaceStore = useWorkspaceStore();

const form = ref({ name: '', slug: '', description: '' });
const saving = ref(false);
const saveError = ref('');
const saveSuccess = ref('');

function resetForm() {
  const ws = workspaceStore.activeWorkspace;
  form.value = {
    name: ws?.name ?? '',
    slug: ws?.slug ?? '',
    description: ws?.description ?? '',
  };
  saveError.value = '';
  saveSuccess.value = '';
}

watch(
  () => workspaceStore.activeWorkspace,
  (ws) => {
    if (ws) {
      form.value = {
        name: ws.name,
        slug: ws.slug,
        description: ws.description ?? '',
      };
    }
  },
  { immediate: true }
);

async function save() {
  const ws = workspaceStore.activeWorkspace;
  if (!ws) return;
  saving.value = true;
  saveError.value = '';
  saveSuccess.value = '';
  try {
    await workspaceStore.updateWorkspace(ws.id, {
      name: form.value.name,
      slug: form.value.slug || undefined,
      description: form.value.description || null,
    });
    saveSuccess.value = 'Saved successfully.';
  } catch (e) {
    saveError.value = e.response?.data?.message ?? 'Failed to save.';
  } finally {
    saving.value = false;
  }
}
</script>
