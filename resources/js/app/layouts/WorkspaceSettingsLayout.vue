<template>
  <div class="space-y-6">
    <div class="flex items-center gap-4">
      <router-link to="/app/settings" class="text-sm text-gray-600 hover:text-gray-900">
        ← Back to Settings
      </router-link>
    </div>
    <h1 class="text-2xl font-semibold tracking-tight">Workspace Settings</h1>

    <template v-if="!workspaceStore.activeWorkspace">
      <Card>
        <CardContent class="py-8 text-center">
          <p class="text-sm text-gray-600">No workspace selected. Create or select a workspace first.</p>
          <router-link to="/app" class="mt-4 inline-block text-sm font-medium text-indigo-600 hover:text-indigo-700">
            Go to Dashboard
          </router-link>
        </CardContent>
      </Card>
    </template>

    <template v-else>
      <div class="flex flex-col gap-6 lg:flex-row">
        <nav class="w-full shrink-0 lg:w-56">
          <ul class="space-y-0.5">
            <li v-for="item in navItems" :key="item.path">
              <router-link
                :to="item.path"
                active-class="bg-gray-100 font-medium text-gray-900"
                class="block rounded-lg px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900"
              >
                {{ item.label }}
              </router-link>
            </li>
          </ul>
        </nav>
        <div class="min-w-0 flex-1">
          <router-view />
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useWorkspaceStore } from '../stores/workspaceStore';
import Card from '../components/ui/Card.vue';
import CardContent from '../components/ui/CardContent.vue';

const workspaceStore = useWorkspaceStore();

const navItems = computed(() => [
  { path: '/app/settings/workspace/general', label: 'General' },
  { path: '/app/settings/workspace/members', label: 'Members' },
  { path: '/app/settings/workspace/preferences', label: 'Preferences' },
  { path: '/app/settings/workspace/branding', label: 'Branding' },
  { path: '/app/settings/workspace/audit-logs', label: 'Audit Logs' },
  { path: '/app/settings/workspace/danger', label: 'Danger Zone' },
]);
</script>
