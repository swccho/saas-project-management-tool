<template>
  <div class="block rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-shadow hover:shadow-md">
    <router-link :to="`/projects/${project.id}`" class="block">
      <div class="flex items-start justify-between gap-2">
        <div class="min-w-0 flex-1">
          <h3 class="truncate font-medium text-gray-900">{{ project.name }}</h3>
          <p class="mt-1 text-xs text-gray-500">{{ project.key }}</p>
          <p v-if="project.description" class="mt-2 line-clamp-2 text-sm text-gray-600">
            {{ project.description }}
          </p>
        </div>
        <div class="flex shrink-0 items-center gap-1">
          <button
            type="button"
            class="rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-amber-500"
            :class="{ 'text-amber-500': localIsFavorite }"
            :aria-label="localIsFavorite ? 'Remove from favorites' : 'Add to favorites'"
            @click.stop.prevent="toggleFavorite"
          >
            <Star :class="['h-4 w-4', localIsFavorite && 'fill-current']" />
          </button>
          <span
            v-if="project.color"
            class="h-3 w-3 rounded-full"
            :style="{ backgroundColor: project.color }"
          />
        </div>
      </div>
      <div v-if="project.status === 'archived'" class="mt-2">
        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">
          Archived
        </span>
      </div>
    </router-link>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Star } from 'lucide-vue-next';
import { projectService } from '../../services/projectService';
import { useWorkspaceStore } from '../../stores/workspaceStore';

const props = defineProps({
  project: { type: Object, required: true },
});

const emit = defineEmits(['favorite-changed']);

const workspaceStore = useWorkspaceStore();
const localIsFavorite = ref(props.project.is_favorite ?? false);

watch(() => props.project.is_favorite, (v) => { localIsFavorite.value = v ?? false; });

async function toggleFavorite(e) {
  e.preventDefault();
  e.stopPropagation();
  const wid = workspaceStore.activeWorkspaceId;
  if (!wid) return;
  const prev = localIsFavorite.value;
  localIsFavorite.value = !prev;
  try {
    if (prev) {
      await projectService.unfavorite(wid, props.project.id);
    } else {
      await projectService.favorite(wid, props.project.id);
    }
    emit('favorite-changed');
    window.dispatchEvent(new CustomEvent('favorites-changed'));
  } catch {
    localIsFavorite.value = prev;
  }
}
</script>
