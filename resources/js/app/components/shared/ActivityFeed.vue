<template>
  <div class="space-y-4">
    <div v-if="loading" class="flex justify-center py-8">
      <div class="h-8 w-8 animate-spin rounded-full border-2 border-indigo-600 border-t-transparent" />
    </div>
    <EmptyState
      v-else-if="activities.length === 0"
      title="No activity yet"
      :compact="true"
      :icon="Activity"
    />
    <div v-else class="space-y-4">
      <div
        v-for="a in activities"
        :key="a.id"
        class="flex gap-3"
      >
        <Avatar :name="a.actor?.name" :status="presenceStatus(a.actor?.id)" size="sm" />
        <div class="min-w-0 flex-1">
          <p class="text-sm text-gray-900">{{ a.message }}</p>
          <p class="text-xs text-gray-500">{{ formatTime(a.created_at) }}</p>
          <router-link
            v-if="a.meta?.task_id && projectId"
            :to="`/projects/${projectId}/board?task=${a.meta.task_id}`"
            class="mt-1 inline-block text-xs text-indigo-600 hover:underline"
          >
            View task
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Activity } from 'lucide-vue-next';
import Avatar from '../ui/Avatar.vue';
import EmptyState from './EmptyState.vue';
import { usePresenceStore } from '../../stores/presenceStore';

const props = defineProps({
  activities: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false },
  projectId: { type: [Number, String], default: null },
});

const presenceStore = usePresenceStore();

function presenceStatus(userId) {
  return userId ? presenceStore.getStatus(userId) : '';
}

function formatTime(d) {
  if (!d) return '';
  const date = new Date(d);
  const now = new Date();
  const diff = now - date;
  if (diff < 60000) return 'Just now';
  if (diff < 3600000) return `${Math.floor(diff / 60000)}m ago`;
  if (diff < 86400000) return `${Math.floor(diff / 3600000)}h ago`;
  return date.toLocaleDateString(undefined, {
    month: 'short',
    day: 'numeric',
    year: date.getFullYear() !== now.getFullYear() ? 'numeric' : undefined,
  });
}
</script>
