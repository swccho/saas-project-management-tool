<template>
  <div ref="rootRef" class="relative inline-block">
    <button
      type="button"
      class="flex min-w-[7rem] items-center gap-1.5 rounded-md border border-gray-200 bg-white px-2 py-1.5 shadow-sm hover:bg-gray-50"
      @click="open = !open"
    >
      <Tag class="h-4 w-4 shrink-0 text-gray-400" />
      <template v-if="selectedLabels.length > 0">
        <span
          v-for="l in selectedLabels.slice(0, 2)"
          :key="l.id"
          class="rounded px-1.5 py-0.5 text-xs"
          :style="{ backgroundColor: (l.color || '#6366F1') + '30', color: l.color || '#6366F1' }"
        >
          {{ l.name }}
        </span>
        <span v-if="selectedLabels.length > 2" class="text-sm text-gray-600">
          +{{ selectedLabels.length - 2 }}
        </span>
      </template>
      <template v-else>
        <span class="text-sm text-gray-700">All labels</span>
      </template>
      <ChevronDown class="ml-auto h-4 w-4 shrink-0 text-gray-400" />
    </button>
    <Teleport v-if="open" to="body">
      <div
        ref="menuRef"
        class="z-50 max-h-64 min-w-[12rem] overflow-y-auto overflow-x-hidden rounded-lg border border-gray-200 bg-white py-1 shadow-lg"
        :style="menuStyle"
        @click.stop
      >
        <button
          type="button"
          class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm hover:bg-gray-100"
          :class="{ 'bg-indigo-50 text-indigo-700': selectedIds.length === 0 }"
          @click="clearSelection"
        >
          <Tag class="h-4 w-4 shrink-0 text-gray-400" />
          <span>All labels</span>
        </button>
        <button
          v-for="l in labels"
          :key="l.id"
          type="button"
          class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm hover:bg-gray-100"
          :class="{ 'bg-indigo-50 text-indigo-700': isSelected(l.id) }"
          @click="toggle(l.id)"
        >
          <span
            class="flex h-4 w-4 shrink-0 items-center justify-center rounded border"
            :class="isSelected(l.id) ? 'border-indigo-600 bg-indigo-600' : 'border-gray-300'"
          >
            <Check v-if="isSelected(l.id)" class="h-3 w-3 text-white" />
          </span>
          <span
            class="rounded px-1.5 py-0.5 text-xs"
            :style="{ backgroundColor: (l.color || '#6366F1') + '30', color: l.color || '#6366F1' }"
          >
            {{ l.name }}
          </span>
        </button>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue';
import { Tag, ChevronDown, Check } from 'lucide-vue-next';

const props = defineProps({
  modelValue: { type: Array, default: () => [] },
  labels: { type: Array, default: () => [] },
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

const selectedLabels = computed(() => {
  return (props.labels ?? []).filter((l) => selectedIds.value.includes(Number(l.id)));
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
