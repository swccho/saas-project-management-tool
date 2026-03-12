<template>
  <div ref="rootRef" class="relative">
    <button
      type="button"
      class="flex items-center gap-1.5 rounded-lg border border-gray-300 px-2.5 py-1.5 text-sm hover:bg-gray-50"
      @click="open = !open"
    >
      <span>Views</span>
      <span v-if="savedViews.length > 0" class="rounded bg-gray-200 px-1.5 py-0.5 text-xs">
        {{ savedViews.length }}
      </span>
    </button>
    <div
      v-if="open"
      class="absolute left-0 top-full z-50 mt-1 min-w-[200px] rounded-lg border border-gray-200 bg-white py-1 shadow-lg"
    >
      <div v-if="savedViews.length === 0" class="px-3 py-4 text-center text-sm text-gray-500">
        No saved views. Save your current filters as a view.
      </div>
      <template v-else>
        <button
          v-for="v in savedViews"
          :key="v.id"
          type="button"
          class="flex w-full items-center justify-between px-3 py-2 text-left text-sm hover:bg-gray-50"
          @click="applyView(v)"
        >
          <span>{{ v.name }}</span>
          <button
            type="button"
            class="rounded p-0.5 text-gray-400 hover:bg-gray-200 hover:text-gray-600"
            @click.stop="deleteView(v)"
          >
            ×
          </button>
        </button>
      </template>
      <div class="border-t border-gray-100 px-2 py-2">
        <button
          v-if="!showSaveForm"
          type="button"
          class="w-full rounded px-2 py-1.5 text-left text-sm text-indigo-600 hover:bg-indigo-50"
          @click="showSaveForm = true"
        >
          Save current view
        </button>
        <form v-else class="space-y-2 px-2" @submit.prevent="submitSave">
          <input
            ref="nameInput"
            v-model="newViewName"
            type="text"
            placeholder="View name"
            class="w-full rounded border px-2 py-1.5 text-sm"
          />
          <div class="flex gap-1">
            <button type="submit" class="rounded bg-indigo-600 px-2 py-1 text-sm text-white">
              Save
            </button>
            <button
              type="button"
              class="rounded px-2 py-1 text-sm text-gray-600 hover:bg-gray-100"
              @click="showSaveForm = false; newViewName = ''"
            >
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, nextTick, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  savedViews: { type: Array, default: () => [] },
  saveView: { type: Function, required: true },
  deleteViewFn: { type: Function, required: true },
});

const emit = defineEmits(['apply-view']);

const open = ref(false);
const rootRef = ref(null);
const showSaveForm = ref(false);
const newViewName = ref('');
const nameInput = ref(null);

function applyView(view) {
  emit('apply-view', view);
  open.value = false;
}

async function submitSave() {
  const name = newViewName.value?.trim();
  if (!name) return;
  await props.saveView(name);
  showSaveForm.value = false;
  newViewName.value = '';
  open.value = false;
}

function deleteView(view) {
  props.deleteViewFn(view);
}

function handleClickOutside(e) {
  if (rootRef.value && !rootRef.value.contains(e.target)) open.value = false;
}

onMounted(() => document.addEventListener('click', handleClickOutside));
onUnmounted(() => document.removeEventListener('click', handleClickOutside));
</script>
