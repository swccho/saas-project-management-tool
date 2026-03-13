<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-semibold text-gray-900">Notifications</h1>
      <div class="flex items-center gap-2">
        <select
          v-model="filter"
          class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
        >
          <option value="all">All</option>
          <option value="unread">Unread only</option>
        </select>
        <Button
          v-if="unreadCount > 0"
          size="sm"
          variant="secondary"
          @click="markAllRead"
        >
          Mark all read
        </Button>
      </div>
    </div>

    <div v-if="loading" class="flex justify-center py-12">
      <div class="h-8 w-8 animate-spin rounded-full border-2 border-indigo-600 border-t-transparent" />
    </div>
    <EmptyState
      v-else-if="notifications.length === 0"
      title="No notifications yet"
      description="When you get mentioned, assigned to tasks, or receive updates, they'll appear here."
    />
    <div v-else class="space-y-1">
      <div
        v-for="n in notifications"
        :key="n.id"
        :class="[
          'flex flex-col gap-1 rounded-lg border px-4 py-3',
          n.is_read ? 'border-gray-100 bg-white' : 'border-indigo-100 bg-indigo-50/30',
        ]"
      >
        <div class="flex items-start justify-between gap-4">
          <div class="min-w-0 flex-1">
            <p class="font-medium text-gray-900">{{ n.title }}</p>
            <p v-if="n.body" class="mt-0.5 text-sm text-gray-600">{{ n.body }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ formatTime(n.created_at) }}</p>
          </div>
          <div class="flex shrink-0 gap-2">
            <Button
              v-if="!n.is_read"
              size="sm"
              variant="secondary"
              @click="markRead(n)"
            >
              Mark read
            </Button>
            <router-link
              v-if="n.data?.task_id && n.data?.project_id"
              :to="`/projects/${n.data.project_id}/board?task=${n.data.task_id}`"
              class="text-sm text-indigo-600 hover:underline"
            >
              View task
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import Button from '../../components/ui/Button.vue';
import EmptyState from '../../components/shared/EmptyState.vue';
import { notificationService } from '../../services/notificationService';

const filter = ref('all');
const notifications = ref([]);
const loading = ref(false);
const unreadCount = ref(0);

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

async function fetchNotifications() {
  loading.value = true;
  try {
    const data = await notificationService.list({
      unread_only: filter.value === 'unread',
    });
    const list = Array.isArray(data) ? data : (data?.data ?? []);
    notifications.value = list;
    unreadCount.value = notifications.value.filter((n) => !n.is_read).length;
  } catch {
    notifications.value = [];
  } finally {
    loading.value = false;
  }
}

async function markRead(n) {
  try {
    await notificationService.markRead(n.id);
    n.is_read = true;
    unreadCount.value = Math.max(0, unreadCount.value - 1);
  } catch {
    // ignore
  }
}

async function markAllRead() {
  try {
    await notificationService.markAllRead();
    await fetchNotifications();
  } catch {
    // ignore
  }
}

watch(filter, () => fetchNotifications());
onMounted(() => fetchNotifications());
</script>
