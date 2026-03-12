<template>
  <div
    :class="cn(
      'rounded-xl border border-gray-200 bg-white shadow-sm',
      $attrs.class
    )"
  >
    <slot />
  </div>
</template>

<script setup>
import { cn } from '../../lib/utils';
</script>
