<template>
  <div ref="rootRef" class="relative inline-block">
    <button
      type="button"
      class="flex min-w-[7rem] items-center gap-1.5 rounded-md border border-gray-200 bg-white px-2 py-1.5 shadow-sm hover:bg-gray-50"
      @click="open = !open"
    >
      <Users class="h-4 w-4 shrink-0 text-gray-400" />
      <template v-if="selectedMembers.length > 0">
        <span class="truncate text-sm text-gray-700">
          {{ selectedMembers.length }} selected
        </span>
      </template>
      <template v-else>
        <span class="text-sm text-gray-700">All assignees</span>
      </template>
      <ChevronDown class="ml-auto h-4 w-4 shrink-0 text-gray-400" />
    </button>
    <Teleport v-if="open" to="body">
      <div
        ref="menuRef"
        class="z-50 min-w-[12rem] overflow-hidden rounded-lg border border-gray-200 bg-white py-1 shadow-lg"
        :style="menuStyle"
        @click.stop
      >
        <button
          type="button"
          class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm hover:bg-gray-100"
          :class="{ 'bg-indigo-50 text-indigo-700': selectedIds.length === 0 }"
          @click="clearSelection"
        >
          <Users class="h-4 w-4 shrink-0 text-gray-400" />
          <span>All assignees</span>
        </button>
        <button
          v-for="m in normalizedMembers"
          :key="m.id"
          type="button"
          class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm hover:bg-gray-100"
          :class="{ 'bg-indigo-50 text-indigo-700': isSelected(m.id) }"
          @click="toggle(m.id)"
        >
          <span
            class="flex h-4 w-4 shrink-0 items-center justify-center rounded border"
            :class="isSelected(m.id) ? 'border-indigo-600 bg-indigo-600' : 'border-gray-300'"
          >
            <Check v-if="isSelected(m.id)" class="h-3 w-3 text-white" />
          </span>
          <Avatar :name="m.name" :src="m.avatar_url" size="sm" />
          <span class="truncate">{{ m.name }}</span>
        </button>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue';
import { Users, ChevronDown, Check } from 'lucide-vue-next';
import Avatar from '../ui/Avatar.vue';

const props = defineProps({
  modelValue: { type: Array, default: () => [] },
  projectMembers: { type: Array, default: () => [] },
});

const emit = defineEmits(['update:modelValue']);

const open = ref(false);
const rootRef = ref(null);
const menuRef = ref(null);
const menuStyle = ref({});

const selectedIds = computed(() => {
  const v = props.modelValue;
  return Array.isArray(v) ? v.map((id) => Number(id)).filter(Boolean) : [];
});

const normalizedMembers = computed(() => {
  return (props.projectMembers ?? []).map((m) => ({
    id: m.user_id ?? m.user?.id ?? m.id,
    name: m.user?.name ?? m.name ?? 'Unknown',
    avatar_url: m.user?.avatar_url ?? null,
  })).filter((m) => m.id != null);
});

const selectedMembers = computed(() => {
  return normalizedMembers.value.filter((m) => selectedIds.value.includes(Number(m.id)));
});

function isSelected(id) {
  return selectedIds.value.includes(Number(id));
}

function toggle(id) {
  const idNum = Number(id);
  const next = isSelected(idNum)
    ? selectedIds.value.filter((x) => x !== idNum)
    : [...selectedIds.value, idNum];
  emit('update:modelValue', next);
}

function clearSelection() {
  emit('update:modelValue', []);
}

function updatePosition() {
  if (!rootRef.value || !menuRef.value || !open.value) return;
  const rect = rootRef.value.getBoundingClientRect();
  menuStyle.value = {
    position: 'fixed',
    top: `${rect.bottom}px`,
    left: `${rect.left}px`,
  };
}

function handleClickOutside(e) {
  const inRoot = rootRef.value?.contains(e.target);
  const inMenu = menuRef.value?.contains(e.target);
  if (!inRoot && !inMenu) open.value = false;
}

watch(open, async (isOpen) => {
  if (isOpen) {
    await nextTick();
    requestAnimationFrame(() => updatePosition());
  }
});

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
  window.addEventListener('scroll', updatePosition, true);
  window.addEventListener('resize', updatePosition);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
  window.removeEventListener('scroll', updatePosition, true);
  window.removeEventListener('resize', updatePosition);
});
</script>
