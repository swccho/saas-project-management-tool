<template>
  <div ref="rootRef" class="relative inline-block">
    <button
      type="button"
      class="flex min-w-[7rem] items-center gap-1.5 rounded-md border border-gray-200 bg-white px-2 py-1.5 shadow-sm hover:bg-gray-50"
      @click="open = !open"
    >
      <Flag class="h-4 w-4 shrink-0 text-gray-400" />
      <span class="truncate text-sm text-gray-700">
        {{ selectedLabels.length === 0 ? 'All priorities' : selectedLabels.length === 1 ? selectedLabels[0] : `${selectedLabels.length} selected` }}
      </span>
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
          :class="{ 'bg-indigo-50 text-indigo-700': selectedValues.length === 0 }"
          @click="clearSelection"
        >
          <Flag class="h-4 w-4 shrink-0 text-gray-400" />
          <span>All priorities</span>
        </button>
        <button
          v-for="opt in options"
          :key="opt.value"
          type="button"
          class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm hover:bg-gray-100"
          :class="{ 'bg-indigo-50 text-indigo-700': isSelected(opt.value) }"
          @click="toggle(opt.value)"
        >
          <span
            class="flex h-4 w-4 shrink-0 items-center justify-center rounded border"
            :class="isSelected(opt.value) ? 'border-indigo-600 bg-indigo-600' : 'border-gray-300'"
          >
            <Check v-if="isSelected(opt.value)" class="h-3 w-3 text-white" />
          </span>
          <span class="truncate capitalize">{{ opt.label }}</span>
        </button>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue';
import { Flag, ChevronDown, Check } from 'lucide-vue-next';

const options = [
  { value: 'low', label: 'Low' },
  { value: 'medium', label: 'Medium' },
  { value: 'high', label: 'High' },
  { value: 'urgent', label: 'Urgent' },
];

const props = defineProps({
  modelValue: { type: Array, default: () => [] },
});

const emit = defineEmits(['update:modelValue']);

const open = ref(false);
const rootRef = ref(null);
const menuRef = ref(null);
const menuStyle = ref({});

const selectedValues = computed(() => {
  const v = props.modelValue;
  return Array.isArray(v) ? [...v] : [];
});

const selectedLabels = computed(() => {
  return selectedValues.value.map((val) => options.find((o) => o.value === val)?.label ?? val);
});

function isSelected(value) {
  return selectedValues.value.includes(value);
}

function toggle(value) {
  const next = isSelected(value)
    ? selectedValues.value.filter((x) => x !== value)
    : [...selectedValues.value, value];
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
