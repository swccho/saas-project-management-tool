<template>
  <label
    :class="cn(
      'flex cursor-pointer items-center gap-3',
      disabled && 'cursor-not-allowed opacity-50',
      $attrs.class
    )"
  >
    <CheckboxRoot
      v-model:checked="model"
      :disabled="disabled"
      :class="cn(
        'flex h-4 w-4 shrink-0 items-center justify-center rounded border border-gray-300 bg-white transition-colors',
        'focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2',
        'data-[state=checked]:border-indigo-600 data-[state=checked]:bg-indigo-600',
        disabled && 'cursor-not-allowed'
      )"
    >
      <CheckboxIndicator class="flex items-center justify-center text-white">
        <Check class="h-3 w-3" />
      </CheckboxIndicator>
    </CheckboxRoot>
    <span v-if="label" class="text-sm text-gray-700">{{ label }}</span>
  </label>
</template>

<script setup>
import { computed } from 'vue';
import { CheckboxIndicator, CheckboxRoot } from 'radix-vue';
import { Check } from 'lucide-vue-next';
import { cn } from '../../lib/utils';

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  label: { type: String, default: '' },
  disabled: { type: Boolean, default: false },
});

const emit = defineEmits(['update:modelValue']);

const model = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val),
});
</script>
