<template>
  <div v-if="!uiStore.sidebarCollapsed && favorites.length > 0" class="mb-4">
    <p class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">
      Favorites
    </p>
    <div class="space-y-0.5">
      <router-link
        v-for="p in favorites"
        :key="p.id"
        :to="`/app/projects/${p.id}`"
        active-class="bg-indigo-50 text-indigo-700"
        class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
      >
        <span
          v-if="p.color"
          class="h-2 w-2 shrink-0 rounded-full"
          :style="{ backgroundColor: p.color }"
        />
        <span class="truncate">{{ p.name }}</span>
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { useWorkspaceStore } from '../../stores/workspaceStore';
import { useUiStore } from '../../stores/uiStore';
import { projectService } from '../../services/projectService';

const workspaceStore = useWorkspaceStore();
const uiStore = useUiStore();
const favorites = ref([]);

async function fetchFavorites() {
  const wid = workspaceStore.activeWorkspaceId;
  if (!wid) {
    favorites.value = [];
    return;
  }
  try {
    favorites.value = await projectService.listFavorites(wid) ?? [];
  } catch {
    favorites.value = [];
  }
}

function handleFavoritesChanged() {
  fetchFavorites();
}

onMounted(() => {
  fetchFavorites();
  window.addEventListener('favorites-changed', handleFavoritesChanged);
});
onUnmounted(() => {
  window.removeEventListener('favorites-changed', handleFavoritesChanged);
});
watch(() => workspaceStore.activeWorkspaceId, fetchFavorites);
</script>
