<template>
  <div class="rounded-lg border-2 border-dashed border-gray-200 bg-gray-50/30 py-6 text-center">
    <p class="text-sm text-gray-500">{{ message }}</p>
    <slot name="action" />
  </div>
</template>

<script setup>
defineProps({
  message: { type: String, default: 'No tasks in this column' },
});
</script>
