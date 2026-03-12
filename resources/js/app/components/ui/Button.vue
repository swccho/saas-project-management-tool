<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    :class="cn(
      'inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none',
      variantClasses[variant],
      sizeClasses[size],
      $attrs.class
    )"
  >
    <span v-if="loading" class="mr-2 h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent" />
    <slot />
  </button>
</template>

<script setup>
import { cn } from '../../lib/utils';

defineProps({
  type: { type: String, default: 'button' },
  variant: { type: String, default: 'primary' },
  size: { type: String, default: 'default' },
  loading: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
});

const variantClasses = {
  primary: 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500',
  secondary: 'bg-gray-100 text-gray-900 hover:bg-gray-200 focus:ring-gray-500',
  ghost: 'hover:bg-gray-100 focus:ring-gray-500',
  destructive: 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
};

const sizeClasses = {
  default: 'h-10',
  sm: 'h-8 text-xs',
  lg: 'h-12 text-base',
};
</script>
