<template>
  <div
    class="flex flex-col items-center justify-center rounded-xl bg-gray-50/40 px-6 py-12 text-center"
    :class="compact ? 'py-6' : 'py-12'"
  >
    <component
      :is="displayIcon"
      class="mx-auto mb-4 h-12 w-12 text-gray-400"
      :class="iconClass"
      aria-hidden="true"
    />
    <h3 v-if="title" class="text-base font-medium text-gray-900">
      {{ title }}
    </h3>
    <p v-if="description" class="mt-1 max-w-sm text-sm text-gray-500">
      {{ description }}
    </p>
    <div v-if="$slots.action || $slots.default" class="mt-4">
      <slot name="action">
        <slot />
      </slot>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { LayoutGrid } from 'lucide-vue-next';

const props = defineProps({
  title: { type: String, default: '' },
  description: { type: String, default: '' },
  icon: { type: Object, default: null },
  iconClass: { type: String, default: '' },
  compact: { type: Boolean, default: false },
});

const displayIcon = computed(() => props.icon ?? LayoutGrid);
</script>
