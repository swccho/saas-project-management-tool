<template>
  <div class="space-y-6">
    <div class="flex items-center gap-4">
      <router-link
        to="/settings"
        class="text-sm text-gray-600 hover:text-gray-900"
      >
        ← Back to Settings
      </router-link>
    </div>
    <h1 class="text-2xl font-semibold tracking-tight">Workspace Settings</h1>

    <template v-if="!workspaceStore.activeWorkspace">
      <Card>
        <CardContent class="py-8 text-center">
          <p class="text-sm text-gray-600">No workspace selected. Create or select a workspace first.</p>
          <router-link to="/" class="mt-4 inline-block text-sm font-medium text-indigo-600 hover:text-indigo-700">
            Go to Dashboard
          </router-link>
        </CardContent>
      </Card>
    </template>

    <template v-else>
      <Card>
        <CardHeader>
          <h2 class="text-lg font-semibold">General</h2>
          <p class="text-sm text-gray-600">Update your workspace name and URL slug.</p>
        </CardHeader>
        <CardContent class="space-y-4">
          <Input
            v-model="form.name"
            label="Name"
            placeholder="My Workspace"
          />
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

      <Card class="border-red-200">
        <CardHeader>
          <h2 class="text-lg font-semibold text-red-700">Danger zone</h2>
          <p class="text-sm text-gray-600">Permanently delete this workspace and all its data.</p>
        </CardHeader>
        <CardContent>
          <Button variant="destructive" @click="showDeleteConfirm = true">
            Delete workspace
          </Button>
        </CardContent>
      </Card>

      <Modal v-model="showDeleteConfirm">
        <div class="space-y-4">
          <h3 class="text-lg font-semibold">Delete workspace?</h3>
          <p class="text-sm text-gray-600">
            This will permanently delete "{{ workspaceStore.activeWorkspace?.name }}" and all its data. This action cannot be undone.
          </p>
          <p class="text-sm text-gray-600">
            Type <strong>{{ workspaceStore.activeWorkspace?.name }}</strong> to confirm.
          </p>
          <Input
            v-model="deleteConfirmText"
            placeholder="Type workspace name to confirm"
          />
          <div class="flex justify-end gap-2">
            <Button variant="secondary" @click="showDeleteConfirm = false">Cancel</Button>
            <Button
              variant="destructive"
              :loading="deleting"
              :disabled="deleteConfirmText !== workspaceStore.activeWorkspace?.name"
              @click="deleteWorkspace"
            >
              Delete workspace
            </Button>
          </div>
        </div>
      </Modal>
    </template>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useWorkspaceStore } from '../../stores/workspaceStore';
import Card from '../../components/ui/Card.vue';
import CardHeader from '../../components/ui/CardHeader.vue';
import CardContent from '../../components/ui/CardContent.vue';
import Input from '../../components/ui/Input.vue';
import Button from '../../components/ui/Button.vue';
import Modal from '../../components/ui/Modal.vue';

const workspaceStore = useWorkspaceStore();
const router = useRouter();

const form = ref({ name: '', slug: '' });
const saving = ref(false);
const saveError = ref('');
const saveSuccess = ref('');
const showDeleteConfirm = ref(false);
const deleteConfirmText = ref('');
const deleting = ref(false);

function resetForm() {
  const ws = workspaceStore.activeWorkspace;
  form.value = {
    name: ws?.name ?? '',
    slug: ws?.slug ?? '',
  };
  saveError.value = '';
  saveSuccess.value = '';
}

watch(
  () => workspaceStore.activeWorkspace,
  (ws) => {
    if (ws) {
      form.value = { name: ws.name, slug: ws.slug };
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
    });
    saveSuccess.value = 'Saved successfully.';
  } catch (e) {
    saveError.value = e.response?.data?.message ?? 'Failed to save.';
  } finally {
    saving.value = false;
  }
}

async function deleteWorkspace() {
  const ws = workspaceStore.activeWorkspace;
  if (!ws || deleteConfirmText.value !== ws.name) return;
  deleting.value = true;
  try {
    await workspaceStore.deleteWorkspace(ws.id);
    showDeleteConfirm.value = false;
    deleteConfirmText.value = '';
    router.push('/');
  } catch (e) {
    saveError.value = e.response?.data?.message ?? 'Failed to delete workspace.';
  } finally {
    deleting.value = false;
  }
}
</script>
