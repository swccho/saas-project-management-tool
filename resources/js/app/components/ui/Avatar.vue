<template>
  <span
    :class="cn(
      'inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-200 text-sm font-medium text-gray-600',
      sizeClasses[size],
      $attrs.class
    )"
  >
    <slot v-if="$slots.default" />
    <span v-else>{{ initials }}</span>
  </span>
</template>

<script setup>
import { computed } from 'vue';
import { cn } from '../../lib/utils';

const props = defineProps({
  name: { type: String, default: '' },
  src: { type: String, default: '' },
  size: { type: String, default: 'default' },
});

const initials = computed(() => {
  if (!props.name) return '?';
  return props.name
    .split(' ')
    .map((n) => n[0])
    .join('')
    .toUpperCase()
    .slice(0, 2);
});

const sizeClasses = {
  default: 'h-10 w-10 text-sm',
  sm: 'h-8 w-8 text-xs',
  lg: 'h-12 w-12 text-base',
};
</script>
