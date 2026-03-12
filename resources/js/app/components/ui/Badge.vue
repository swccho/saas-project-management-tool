<template>
  <span
    :class="cn(
      'inline-flex items-center rounded-md px-2.5 py-0.5 text-xs font-medium',
      variantClasses[variant],
      $attrs.class
    )"
  >
    <slot />
  </span>
</template>

<script setup>
import { cn } from '../../lib/utils';

defineProps({
  variant: { type: String, default: 'default' },
});

const variantClasses = {
  default: 'bg-gray-100 text-gray-800',
  primary: 'bg-indigo-100 text-indigo-800',
  success: 'bg-green-100 text-green-800',
  warning: 'bg-amber-100 text-amber-800',
  destructive: 'bg-red-100 text-red-800',
};
</script>
