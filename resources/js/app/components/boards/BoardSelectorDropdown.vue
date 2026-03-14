<template>
  <div ref="rootRef" class="relative inline-block">
    <button
      type="button"
      class="flex min-w-[7rem] items-center gap-1.5 rounded-md border border-gray-200 bg-white px-2 py-1.5 shadow-sm hover:bg-gray-50"
      @click="open = !open"
    >
      <LayoutDashboard class="h-4 w-4 shrink-0 text-gray-400" />
      <span class="truncate text-sm text-gray-700">
        {{ selectedBoard?.name ?? 'Select board' }}
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
          v-for="b in boards"
          :key="b.id"
          type="button"
          class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm hover:bg-gray-100"
          :class="{ 'bg-indigo-50 text-indigo-700': modelValue === Number(b.id) }"
          @click="select(b.id)"
        >
          <LayoutDashboard class="h-4 w-4 shrink-0 text-gray-400" />
          <span class="truncate">{{ b.name }}</span>
        </button>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue';
import { LayoutDashboard, ChevronDown } from 'lucide-vue-next';

const props = defineProps({
  modelValue: { type: [Number, String], default: null },
  boards: { type: Array, default: () => [] },
});

const emit = defineEmits(['update:modelValue']);

const open = ref(false);
const rootRef = ref(null);
const menuRef = ref(null);
const menuStyle = ref({});

const selectedBoard = computed(() => {
  const id = props.modelValue;
  if (id == null) return null;
  return (props.boards ?? []).find((b) => Number(b.id) === Number(id)) ?? null;
});

function select(id) {
  emit('update:modelValue', Number(id));
  open.value = false;
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
