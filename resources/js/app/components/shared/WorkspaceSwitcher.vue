<template>
  <div ref="rootRef" class="mb-4">
    <button
      type="button"
      class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors"
      :aria-expanded="open"
      aria-haspopup="listbox"
      @click="open = !open"
    >
      <Building2 class="h-5 w-5 shrink-0" />
      <span v-if="!uiStore.sidebarCollapsed" class="flex-1 truncate">
        {{ workspaceStore.activeWorkspace?.name ?? 'Select workspace' }}
      </span>
      <ChevronDown
        v-if="!uiStore.sidebarCollapsed"
        :class="['h-4 w-4 shrink-0 transition-transform', open && 'rotate-180']"
      />
    </button>
    <Transition
      enter-active-class="transition duration-150 ease-out"
      enter-from-class="opacity-0 -translate-y-1"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-100 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-1"
    >
      <div v-if="open && !uiStore.sidebarCollapsed" class="mt-1 space-y-1 rounded-lg border border-gray-200 bg-white py-1 shadow-lg">
      <button
        v-for="ws in workspaceStore.workspaces"
        :key="ws.id"
        type="button"
        :class="[
          'block w-full px-4 py-2 text-left text-sm hover:bg-gray-50',
          workspaceStore.activeWorkspaceId === ws.id && 'bg-indigo-50 font-medium text-indigo-700',
        ]"
        @click="selectWorkspace(ws.id)"
      >
        {{ ws.name }}
      </button>
      <button
        type="button"
        class="block w-full px-4 py-2 text-left text-sm text-indigo-600 hover:bg-indigo-50"
        @click="showCreate = true"
      >
        + Create workspace
      </button>
    </div>
    </Transition>
    <div v-if="showCreate" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30" @click.self="showCreate = false">
      <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
        <h3 class="text-lg font-semibold">Create workspace</h3>
        <form class="mt-4 space-y-4" @submit.prevent="createWorkspace">
          <Input v-model="newName" label="Name" placeholder="My Workspace" required />
          <div class="flex justify-end gap-2">
            <Button type="button" variant="secondary" @click="showCreate = false">Cancel</Button>
            <Button type="submit" :loading="creating">Create</Button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Building2, ChevronDown } from 'lucide-vue-next';
import { useWorkspaceStore } from '../../stores/workspaceStore';
import { useUiStore } from '../../stores/uiStore';
import Button from '../ui/Button.vue';
import Input from '../ui/Input.vue';

const workspaceStore = useWorkspaceStore();
const uiStore = useUiStore();
const open = ref(false);
const rootRef = ref(null);

function handleClickOutside(e) {
  if (rootRef.value && !rootRef.value.contains(e.target)) open.value = false;
}

function handleKeydown(e) {
  if (e.key === 'Escape') {
    open.value = false;
    showCreate.value = false;
  }
}
const showCreate = ref(false);
const newName = ref('');
const creating = ref(false);

onMounted(() => {
  if (workspaceStore.workspaces.length === 0) {
    workspaceStore.fetchWorkspaces();
  }
  document.addEventListener('click', handleClickOutside);
  document.addEventListener('keydown', handleKeydown);
});
onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
  document.removeEventListener('keydown', handleKeydown);
});

function selectWorkspace(id) {
  workspaceStore.setActive(id);
  open.value = false;
}

async function createWorkspace() {
  if (!newName.value.trim()) return;
  creating.value = true;
  try {
    await workspaceStore.createWorkspace(newName.value.trim());
    showCreate.value = false;
    newName.value = '';
    open.value = false;
  } finally {
    creating.value = false;
  }
}
</script>
