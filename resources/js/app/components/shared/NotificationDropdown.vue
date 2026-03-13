<template>
  <div class="relative">
    <button
      type="button"
      class="relative rounded p-1.5 hover:bg-gray-100"
      aria-label="Open notifications"
      @click="toggle"
    >
      <Bell class="h-5 w-5 text-gray-600" aria-hidden="true" />
      <span
        v-if="unreadCount > 0"
        class="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-medium text-white"
      >
        {{ unreadCount > 99 ? '99+' : unreadCount }}
      </span>
    </button>
    <Teleport v-if="open" to="body">
      <div
        class="fixed inset-0 z-40"
        @click="open = false"
      />
      <div
        class="fixed right-6 top-16 z-50 w-96 max-h-[28rem] overflow-hidden rounded-lg border border-gray-200 bg-white shadow-xl"
      >
        <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3">
          <h3 class="font-semibold text-gray-900">Notifications</h3>
          <div class="flex items-center gap-2">
            <button
              v-if="unreadCount > 0"
              type="button"
              class="text-xs text-indigo-600 hover:underline"
              @click="markAllRead"
            >
              Mark all read
            </button>
            <router-link
              to="/notifications"
              class="text-xs text-indigo-600 hover:underline"
              @click="open = false"
            >
              View all
            </router-link>
          </div>
        </div>
        <div v-if="loading" class="flex justify-center py-8">
          <div class="h-6 w-6 animate-spin rounded-full border-2 border-indigo-600 border-t-transparent" />
        </div>
        <div v-else-if="notifications.length === 0" class="py-8 text-center text-sm text-gray-500">
          No notifications
        </div>
        <div v-else class="max-h-80 overflow-y-auto">
          <button
            v-for="n in notifications"
            :key="n.id"
            type="button"
            :class="[
              'flex w-full flex-col gap-0.5 border-b border-gray-100 px-4 py-3 text-left hover:bg-gray-50',
              !n.is_read && 'bg-indigo-50/50',
            ]"
            @click="handleClick(n)"
          >
            <span class="text-sm font-medium text-gray-900">{{ n.title }}</span>
            <span v-if="n.body" class="text-xs text-gray-600 line-clamp-2">{{ n.body }}</span>
            <span class="text-xs text-gray-500">{{ formatTime(n.created_at) }}</span>
          </button>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { Bell } from 'lucide-vue-next';
import { notificationService } from '../../services/notificationService';

const open = ref(false);
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
    const data = await notificationService.list({ unread_only: false });
    const list = Array.isArray(data) ? data : (data?.data ?? []);
    notifications.value = list;
    unreadCount.value = notifications.value.filter((n) => !n.is_read).length;
  } catch {
    notifications.value = [];
  } finally {
    loading.value = false;
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

function handleClick(n) {
  if (!n.is_read) {
    notificationService.markRead(n.id).then(() => {
      n.is_read = true;
      unreadCount.value = Math.max(0, unreadCount.value - 1);
    });
  }
  if (n.data?.task_id && n.data?.project_id && n.data?.workspace_id) {
    window.location.href = `/projects/${n.data.project_id}/board?task=${n.data.task_id}`;
  }
  open.value = false;
}

function toggle() {
  open.value = !open.value;
  if (open.value) fetchNotifications();
}

watch(open, (v) => {
  if (v) fetchNotifications();
});

onMounted(() => {
  fetchNotifications();
});
</script>
