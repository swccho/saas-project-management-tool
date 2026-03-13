<template>
  <div class="space-y-3">
    <div class="flex items-center justify-between">
      <h3 class="text-sm font-semibold text-gray-900">Recent Activity</h3>
      <router-link
        to="/activity"
        class="text-xs text-indigo-600 hover:underline"
      >
        View all
      </router-link>
    </div>
    <div v-if="loading" class="space-y-3">
      <div v-for="i in 3" :key="i" class="flex gap-3">
        <div class="h-8 w-8 shrink-0 animate-pulse rounded-full bg-gray-200" />
        <div class="flex-1 space-y-1">
          <div class="h-3 w-24 animate-pulse rounded bg-gray-200" />
          <div class="h-3 w-16 animate-pulse rounded bg-gray-100" />
        </div>
      </div>
    </div>
    <EmptyState
      v-else-if="activities.length === 0"
      title="No activity yet"
      :compact="true"
      :icon="Activity"
    />
    <div v-else class="space-y-3">
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
            v-if="a.meta?.task_id && a.meta?.project_id"
            :to="`/projects/${a.meta.project_id}/board?task=${a.meta.task_id}`"
            class="mt-0.5 inline-block text-xs text-indigo-600 hover:underline"
          >
            View task
          </router-link>
          <router-link
            v-else-if="a.meta?.project_id"
            :to="`/projects/${a.meta.project_id}`"
            class="mt-0.5 inline-block text-xs text-indigo-600 hover:underline"
          >
            View project
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Activity } from 'lucide-vue-next';
import Avatar from '../ui/Avatar.vue';
import EmptyState from '../shared/EmptyState.vue';
import { usePresenceStore } from '../../stores/presenceStore';

const props = defineProps({
  activities: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false },
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
