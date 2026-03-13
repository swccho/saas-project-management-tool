<template>
  <div class="space-y-3">
    <h4 class="text-sm font-medium text-gray-700">Attachments</h4>
    <div v-if="loading" class="flex justify-center py-4">
      <div class="h-6 w-6 animate-spin rounded-full border-2 border-indigo-600 border-t-transparent" />
    </div>
    <template v-else>
      <EmptyState
        v-if="attachments.length === 0"
        title="No attachments yet"
        :compact="true"
        :icon="Paperclip"
      >
        <Button size="sm" variant="secondary" @click="fileInputRef?.click()">
          Upload file
        </Button>
      </EmptyState>
      <div v-else class="space-y-2">
        <div
          v-for="a in attachments"
          :key="a.id"
          class="flex items-center justify-between gap-2 rounded-lg border border-gray-100 bg-gray-50/50 p-2"
        >
          <button
            type="button"
            class="flex min-w-0 flex-1 items-center gap-2 text-left text-sm text-gray-700 hover:text-indigo-600"
            @click="downloadFile(a)"
          >
            <FileIcon class="h-4 w-4 shrink-0 text-gray-400" />
            <span class="truncate">{{ a.original_name }}</span>
            <span class="shrink-0 text-xs text-gray-500">{{ formatSize(a.size) }}</span>
          </button>
          <div class="flex items-center gap-2">
            <span v-if="a.uploaded_by" class="text-xs text-gray-500">{{ a.uploaded_by.name }}</span>
            <button
              type="button"
              class="rounded p-1 text-gray-400 hover:bg-red-50 hover:text-red-600"
              title="Delete"
              @click="confirmDelete(a)"
            >
              <Trash2 class="h-4 w-4" />
            </button>
          </div>
        </div>
      </div>
      <input
        ref="fileInputRef"
        type="file"
        class="hidden"
        @change="handleFileSelect"
      />
      <div v-if="attachments.length > 0" class="flex items-center gap-2">
        <Button size="sm" variant="secondary" @click="fileInputRef?.click()">
          Upload file
        </Button>
        <span class="text-xs text-gray-500">Max 10MB</span>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { FileIcon, Paperclip, Trash2 } from 'lucide-vue-next';
import Button from '../ui/Button.vue';
import EmptyState from '../shared/EmptyState.vue';

const props = defineProps({
  attachments: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false },
  uploadAttachment: { type: Function, default: null },
  deleteAttachment: { type: Function, default: null },
  downloadAttachment: { type: Function, default: null },
});

const fileInputRef = ref(null);

function formatSize(bytes) {
  if (!bytes) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return `${parseFloat((bytes / Math.pow(k, i)).toFixed(1))} ${sizes[i]}`;
}

async function downloadFile(a) {
  if (props.downloadAttachment) {
    await props.downloadAttachment(a);
  }
}

function handleFileSelect(e) {
  const file = e.target.files?.[0];
  if (!file || !props.uploadAttachment) return;
  if (file.size > 10 * 1024 * 1024) {
    alert('File must not exceed 10MB');
    return;
  }
  props.uploadAttachment(file);
  e.target.value = '';
}

function confirmDelete(a) {
  if (!confirm(`Delete "${a.original_name}"?`)) return;
  props.deleteAttachment?.(a.id);
}
</script>
