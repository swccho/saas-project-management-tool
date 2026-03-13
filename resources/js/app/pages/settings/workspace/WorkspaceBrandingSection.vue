<template>
  <Card>
    <CardHeader>
      <h2 class="text-lg font-semibold">Branding</h2>
      <p class="text-sm text-gray-600">Customize workspace logo, icon, and appearance.</p>
    </CardHeader>
    <CardContent>
      <div v-if="loading" class="space-y-4">
        <div class="h-24 animate-pulse rounded bg-gray-100" />
      </div>
      <form v-else class="space-y-6" @submit.prevent="save">
        <div class="space-y-4">
          <h3 class="text-sm font-medium text-gray-700">Logo</h3>
          <div class="flex items-center gap-6">
            <div class="relative">
              <img
                v-if="branding.logo_url"
                :src="branding.logo_url"
                alt="Logo"
                class="h-16 w-32 object-contain"
              />
              <div
                v-else
                class="flex h-16 w-32 items-center justify-center rounded border-2 border-dashed border-gray-200 text-sm text-gray-400"
              >
                No logo
              </div>
              <label class="mt-2 inline-block">
                <span class="cursor-pointer rounded bg-gray-100 px-3 py-1.5 text-sm hover:bg-gray-200">
                  {{ branding.logo_url ? 'Replace' : 'Upload' }}
                </span>
                <input
                  type="file"
                  accept="image/*"
                  class="hidden"
                  @change="handleLogoUpload"
                />
              </label>
              <Button
                v-if="branding.logo_url"
                variant="ghost"
                size="sm"
                class="ml-2 text-red-600"
                @click.prevent="removeLogo"
              >
                Remove
              </Button>
            </div>
          </div>
        </div>
        <div class="space-y-4">
          <h3 class="text-sm font-medium text-gray-700">Icon</h3>
          <div class="flex items-center gap-6">
            <div class="relative">
              <img
                v-if="branding.icon_url"
                :src="branding.icon_url"
                alt="Icon"
                class="h-12 w-12 object-contain"
              />
              <div
                v-else
                class="flex h-12 w-12 items-center justify-center rounded border-2 border-dashed border-gray-200 text-xs text-gray-400"
              >
                No icon
              </div>
              <label class="mt-2 inline-block">
                <span class="cursor-pointer rounded bg-gray-100 px-3 py-1.5 text-sm hover:bg-gray-200">
                  {{ branding.icon_url ? 'Replace' : 'Upload' }}
                </span>
                <input
                  type="file"
                  accept="image/*"
                  class="hidden"
                  @change="handleIconUpload"
                />
              </label>
              <Button
                v-if="branding.icon_url"
                variant="ghost"
                size="sm"
                class="ml-2 text-red-600"
                @click.prevent="removeIcon"
              >
                Remove
              </Button>
            </div>
          </div>
        </div>
        <div class="space-y-4">
          <h3 class="text-sm font-medium text-gray-700">Colors</h3>
          <div class="space-y-1">
            <label class="block text-sm font-medium text-gray-700">Accent color</label>
            <div class="flex items-center gap-2">
              <input
                v-model="form.accent_color"
                type="color"
                class="h-10 w-16 cursor-pointer rounded border border-gray-300"
              />
              <Input
                v-model="form.accent_color"
                placeholder="#6366f1"
                class="w-32"
              />
            </div>
          </div>
        </div>
        <div class="space-y-4">
          <h3 class="text-sm font-medium text-gray-700">Description</h3>
          <div class="space-y-1">
            <label class="block text-sm font-medium text-gray-700">Short description</label>
            <textarea
              v-model="form.short_description"
              rows="2"
              placeholder="Brief workspace description"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
            />
          </div>
        </div>
        <div class="flex gap-2">
          <Button :loading="saving" type="submit">Save changes</Button>
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
import Input from '../../../components/ui/Input.vue';
import Button from '../../../components/ui/Button.vue';

const workspaceStore = useWorkspaceStore();

const branding = reactive({
  logo_url: null,
  icon_url: null,
  accent_color: null,
  short_description: null,
});

const form = reactive({
  accent_color: '#6366f1',
  short_description: '',
});

const loading = ref(false);
const saving = ref(false);
const saveSuccess = ref('');
const saveError = ref('');

async function fetchBranding() {
  const ws = workspaceStore.activeWorkspace;
  if (!ws) return;
  loading.value = true;
  try {
    const data = await workspaceService.getBranding(ws.id) ?? {};
    branding.logo_url = data.logo_url;
    branding.icon_url = data.icon_url;
    branding.accent_color = data.accent_color;
    branding.short_description = data.short_description;
    form.accent_color = data.accent_color ?? '#6366f1';
    form.short_description = data.short_description ?? '';
  } finally {
    loading.value = false;
  }
}

async function handleLogoUpload(e) {
  const file = e.target.files?.[0];
  if (!file) return;
  const ws = workspaceStore.activeWorkspace;
  if (!ws) return;
  try {
    const data = await workspaceService.uploadLogo(ws.id, file);
    branding.logo_url = data.logo_url;
  } catch (err) {
    saveError.value = err.response?.data?.message ?? 'Failed to upload logo.';
  }
  e.target.value = '';
}

async function handleIconUpload(e) {
  const file = e.target.files?.[0];
  if (!file) return;
  const ws = workspaceStore.activeWorkspace;
  if (!ws) return;
  try {
    const data = await workspaceService.uploadIcon(ws.id, file);
    branding.icon_url = data.icon_url;
  } catch (err) {
    saveError.value = err.response?.data?.message ?? 'Failed to upload icon.';
  }
  e.target.value = '';
}

async function removeLogo() {
  const ws = workspaceStore.activeWorkspace;
  if (!ws) return;
  try {
    await workspaceService.removeLogo(ws.id);
    branding.logo_url = null;
  } catch (err) {
    saveError.value = err.response?.data?.message ?? 'Failed to remove logo.';
  }
}

async function removeIcon() {
  const ws = workspaceStore.activeWorkspace;
  if (!ws) return;
  try {
    await workspaceService.removeIcon(ws.id);
    branding.icon_url = null;
  } catch (err) {
    saveError.value = err.response?.data?.message ?? 'Failed to remove icon.';
  }
}

async function save() {
  const ws = workspaceStore.activeWorkspace;
  if (!ws) return;
  saving.value = true;
  saveError.value = '';
  saveSuccess.value = '';
  try {
    const data = await workspaceService.updateBranding(ws.id, {
      accent_color: form.accent_color || null,
      short_description: form.short_description || null,
    });
    branding.accent_color = data.accent_color;
    branding.short_description = data.short_description;
    saveSuccess.value = 'Saved successfully.';
  } catch (e) {
    saveError.value = e.response?.data?.message ?? 'Failed to save.';
  } finally {
    saving.value = false;
  }
}

onMounted(fetchBranding);
watch(() => workspaceStore.activeWorkspaceId, fetchBranding);
</script>
