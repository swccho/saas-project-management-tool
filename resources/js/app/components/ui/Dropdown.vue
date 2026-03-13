<template>
  <div ref="rootRef" class="relative inline-block">
    <div @click="open = !open">
      <slot name="trigger" />
    </div>
    <Teleport v-if="open" to="body">
      <div
        class="absolute z-50 min-w-[8rem] overflow-hidden rounded-lg border border-gray-200 bg-white py-1 shadow-lg"
        :style="dropdownStyle"
      >
        <slot />
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const open = ref(false);
const rootRef = ref(null);
const dropdownStyle = computed(() => ({}));

function handleClickOutside(e) {
  if (rootRef.value && !rootRef.value.contains(e.target)) open.value = false;
}

onMounted(() => document.addEventListener('click', handleClickOutside));
onUnmounted(() => document.removeEventListener('click', handleClickOutside));
</script>
