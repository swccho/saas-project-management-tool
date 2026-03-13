<template>
  <Teleport v-if="modelValue" to="body">
    <div class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="fixed inset-0 bg-black/50" @click="$emit('update:modelValue', false)" />
      <div
        class="relative z-10 w-full max-w-lg rounded-xl bg-white p-6 shadow-xl"
        role="dialog"
      >
        <slot />
      </div>
    </div>
  </Teleport>
</template>

<script setup>
defineProps({
  modelValue: { type: Boolean, default: false },
});

defineEmits(['update:modelValue']);
</script>
