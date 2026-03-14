<template>
  <div class="space-y-1">
    <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    <div class="relative">
      <input
        :id="id"
        :type="effectiveType"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :class="cn(
          'flex h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
          showPasswordToggle && 'pr-10',
          error && 'border-red-500 focus:ring-red-500',
          $attrs.class
        )"
        @input="$emit('update:modelValue', $event.target.value)"
      />
      <button
        v-if="showPasswordToggle"
        type="button"
        :aria-label="effectiveType === 'password' ? 'Show password' : 'Hide password'"
        class="absolute right-2 top-1/2 -translate-y-1/2 rounded p-1.5 text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
        @click="passwordVisible = !passwordVisible"
      >
        <Eye v-if="effectiveType === 'password'" class="h-4 w-4" />
        <EyeOff v-else class="h-4 w-4" />
      </button>
    </div>
    <p v-if="error" class="text-sm text-red-500">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Eye, EyeOff } from 'lucide-vue-next';
import { cn } from '../../lib/utils';

const props = defineProps({
  modelValue: { type: [String, Number], default: '' },
  type: { type: String, default: 'text' },
  label: { type: String, default: '' },
  placeholder: { type: String, default: '' },
  error: { type: String, default: '' },
  required: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  showPasswordToggle: { type: Boolean, default: undefined },
});

defineEmits(['update:modelValue']);

const id = `input-${Math.random().toString(36).slice(2)}`;
const passwordVisible = ref(false);

const showPasswordToggle = computed(() => {
  if (props.showPasswordToggle !== undefined) return props.showPasswordToggle;
  return props.type === 'password';
});

const effectiveType = computed(() => {
  if (props.type !== 'password') return props.type;
  return passwordVisible.value ? 'text' : 'password';
});
</script>
