<template>
  <span class="relative inline-block">
    <span
      :class="cn(
        'inline-flex shrink-0 items-center justify-center rounded-full bg-gray-200 text-sm font-medium text-gray-600',
        sizeClasses[size],
        $attrs.class
      )"
    >
      <slot v-if="$slots.default" />
      <span v-else>{{ initials }}</span>
    </span>
    <span
      v-if="status"
      :class="[
        'absolute bottom-0 right-0 block rounded-full border-2 border-white ring-2 ring-white',
        statusSizeClasses[size],
        statusColorClasses[status] ?? 'bg-gray-400',
      ]"
      :title="status"
    />
  </span>
</template>

<script setup>
import { computed } from 'vue';
import { cn } from '../../lib/utils';

const props = defineProps({
  name: { type: String, default: '' },
  src: { type: String, default: '' },
  size: { type: String, default: 'default' },
  status: { type: String, default: '' },
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
  xs: 'h-6 w-6 text-[10px]',
  sm: 'h-8 w-8 text-xs',
  lg: 'h-12 w-12 text-base',
};

const statusSizeClasses = {
  default: 'h-3 w-3',
  xs: 'h-1.5 w-1.5',
  sm: 'h-2.5 w-2.5',
  lg: 'h-3.5 w-3.5',
};

const statusColorClasses = {
  online: 'bg-green-500',
  away: 'bg-yellow-500',
  offline: 'bg-gray-400',
};
</script>
