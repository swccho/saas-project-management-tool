<template>
  <div class="flex min-h-screen bg-[#FAFAFA]">
    <aside
      :class="[
        'flex flex-col border-r border-gray-200 bg-white transition-all',
        uiStore.sidebarCollapsed ? 'w-16' : 'w-64',
      ]"
    >
      <div class="flex h-14 items-center border-b border-gray-200 px-4">
        <span v-if="!uiStore.sidebarCollapsed" class="text-lg font-semibold text-gray-900">
          SaaS PM
        </span>
      </div>
      <nav class="flex-1 space-y-1 p-4">
        <WorkspaceSwitcher />
        <SidebarFavorites />
        <router-link
          v-for="item in navItems"
          :key="item.path"
          :to="item.path"
          :class="[
            'flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-gray-100',
            isNavItemActive(item) ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700',
          ]"
        >
          <component :is="item.icon" class="h-5 w-5 shrink-0" />
          <span v-if="!uiStore.sidebarCollapsed">{{ item.label }}</span>
        </router-link>
      </nav>
    </aside>
    <div class="flex flex-1 flex-col">
      <header class="flex h-14 items-center justify-between border-b border-gray-200 bg-white px-6">
        <div class="flex items-center gap-4">
          <button
            type="button"
            class="rounded p-1 hover:bg-gray-100"
            @click="uiStore.toggleSidebar"
          >
            <LayoutDashboard class="h-5 w-5" />
          </button>
          <input
            type="search"
            placeholder="Search..."
            class="w-64 rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
          />
        </div>
        <div class="flex items-center gap-2">
          <NotificationDropdown />
          <UserMenu />
        </div>
      </header>
      <main class="flex-1 p-6">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { onMounted, onUnmounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import { LayoutDashboard, FolderKanban, ListTodo, Settings, Activity, Calendar } from 'lucide-vue-next';
import { useAuthStore } from '../stores/authStore';
import { useUiStore } from '../stores/uiStore';
import { useWorkspaceStore } from '../stores/workspaceStore';
import { usePresenceStore } from '../stores/presenceStore';
import { presenceService } from '../services/presenceService';
import WorkspaceSwitcher from '../components/shared/WorkspaceSwitcher.vue';
import SidebarFavorites from '../components/shared/SidebarFavorites.vue';
import NotificationDropdown from '../components/shared/NotificationDropdown.vue';
import UserMenu from '../components/shared/UserMenu.vue';

const authStore = useAuthStore();
const uiStore = useUiStore();
const presenceStore = usePresenceStore();
const route = useRoute();

onMounted(() => {
  if (authStore.isAuthenticated) {
    presenceService.startHeartbeat();
    presenceStore.fetchPresence();
  }
});

onUnmounted(() => {
  presenceService.stopHeartbeat();
});

watch(() => useWorkspaceStore().activeWorkspaceId, () => {
  if (authStore.isAuthenticated) {
    presenceStore.fetchPresence();
  }
}, { immediate: false });

const navItems = [
  { path: '/', label: 'Dashboard', icon: LayoutDashboard },
  { path: '/activity', label: 'Activity', icon: Activity },
  { path: '/projects', label: 'Projects', icon: FolderKanban },
  { path: '/my-tasks', label: 'My Tasks', icon: ListTodo },
  { path: '/calendar', label: 'Calendar', icon: Calendar },
  { path: '/settings', label: 'Settings', icon: Settings },
];

function isNavItemActive(item) {
  const path = route.path;
  if (item.path === '/') {
    return path === '/';
  }
  return path === item.path || path.startsWith(item.path + '/');
}
</script>
