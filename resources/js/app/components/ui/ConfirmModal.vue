<template>
  <Teleport v-if="modelValue" to="body">
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-black/50" @click="handleCancel" />
      <div
        class="relative z-10 w-full max-w-md rounded-xl bg-white p-6 shadow-xl"
        role="dialog"
        aria-modal="true"
        :aria-labelledby="titleId"
      >
        <h3 :id="titleId" class="text-lg font-semibold text-gray-900">
          {{ title }}
        </h3>
        <p class="mt-2 text-sm text-gray-600">
          {{ message }}
        </p>
        <div class="mt-6 flex justify-end gap-2">
          <Button
            v-if="!alertOnly"
            variant="secondary"
            @click="handleCancel"
          >
            {{ cancelLabel }}
          </Button>
          <Button
            :variant="confirmVariant"
            @click="handleConfirm"
          >
            {{ confirmLabel }}
          </Button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import Button from './Button.vue';

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  title: { type: String, default: 'Confirm' },
  message: { type: String, default: '' },
  confirmLabel: { type: String, default: 'Confirm' },
  cancelLabel: { type: String, default: 'Cancel' },
  alertOnly: { type: Boolean, default: false },
  confirmVariant: { type: String, default: 'primary' },
});

const emit = defineEmits(['update:modelValue', 'confirm', 'cancel']);

const titleId = 'confirm-modal-title';

function handleConfirm() {
  emit('confirm');
  emit('update:modelValue', false);
}

function handleCancel() {
  emit('cancel');
  emit('update:modelValue', false);
}
</script>
