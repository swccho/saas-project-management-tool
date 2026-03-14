<template>
  <div class="space-y-1.5">
    <label class="block text-xs font-medium text-gray-500">Assignee</label>
    <div ref="rootRef" class="relative">
      <button
        type="button"
        class="flex w-full items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-left text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
        @click="open = !open"
      >
        <Avatar
          v-if="selectedMember"
          :name="selectedMember.user?.name"
          :src="selectedMember.user?.avatar_url"
          size="sm"
        />
        <span v-else class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-xs text-gray-500">
          ?
        </span>
        <span class="flex-1 truncate text-gray-900">
          {{ selectedMember?.user?.name ?? 'Select assignee' }}
        </span>
        <ChevronDown class="h-4 w-4 shrink-0 text-gray-400" />
      </button>
      <div
        v-if="open"
        class="absolute left-0 right-0 top-full z-50 mt-1 max-h-48 overflow-auto rounded-lg border border-gray-200 bg-white py-1 shadow-lg"
      >
        <button
          type="button"
          class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm hover:bg-gray-100"
          :class="{ 'bg-indigo-50 text-indigo-700': !modelValue }"
          @click="select(null)"
        >
          <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-xs text-gray-500">—</span>
          <span>Unassigned</span>
        </button>
        <button
          v-for="m in normalizedMembers"
          :key="m.user_id"
          type="button"
          class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm hover:bg-gray-100"
          :class="{ 'bg-indigo-50 text-indigo-700': modelValue === m.user_id }"
          @click="select(m.user_id)"
        >
          <Avatar :name="m.user?.name" :src="m.user?.avatar_url" size="sm" />
          <span class="truncate">{{ m.user?.name }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import Avatar from '../ui/Avatar.vue';
import { ChevronDown } from 'lucide-vue-next';

const props = defineProps({
  modelValue: { type: [Number, String], default: null },
  members: { type: Array, default: () => [] },
});

const emit = defineEmits(['update:modelValue']);

const open = ref(false);
const rootRef = ref(null);

const normalizedMembers = computed(() =>
  (props.members ?? []).filter((m) => m.user_id != null && m.user?.name)
);

const selectedMember = computed(() =>
  normalizedMembers.value.find((m) => m.user_id === props.modelValue)
);

function select(userId) {
  emit('update:modelValue', userId ? Number(userId) : null);
  open.value = false;
}

function handleClickOutside(e) {
  if (rootRef.value && !rootRef.value.contains(e.target)) open.value = false;
}

onMounted(() => document.addEventListener('click', handleClickOutside));
onUnmounted(() => document.removeEventListener('click', handleClickOutside));
</script>
