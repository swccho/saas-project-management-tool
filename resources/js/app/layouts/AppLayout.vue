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
        <router-link
          v-for="item in navItems"
          :key="item.path"
          :to="item.path"
          active-class="bg-indigo-50 text-indigo-700"
          class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
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
          <span class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-xs">🔔</span>
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
import { LayoutDashboard, FolderKanban, ListTodo, Settings } from 'lucide-vue-next';
import { useUiStore } from '../stores/uiStore';
import WorkspaceSwitcher from '../components/shared/WorkspaceSwitcher.vue';
import UserMenu from '../components/shared/UserMenu.vue';

const uiStore = useUiStore();

const navItems = [
  { path: '/', label: 'Dashboard', icon: LayoutDashboard },
  { path: '/projects', label: 'Projects', icon: FolderKanban },
  { path: '/my-tasks', label: 'My Tasks', icon: ListTodo },
  { path: '/settings', label: 'Settings', icon: Settings },
];
</script>
