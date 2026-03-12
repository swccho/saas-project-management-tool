<template>
  <div
    v-if="modelValue"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
    @click.self="$emit('update:modelValue', false)"
  >
    <div
      class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl"
      role="dialog"
      aria-labelledby="create-project-title"
    >
      <h3 id="create-project-title" class="text-lg font-semibold">Create project</h3>
      <form class="mt-4 space-y-4" @submit.prevent="submit">
        <Input v-model="form.name" label="Name" placeholder="My Project" required />
        <div>
          <label class="block text-sm font-medium text-gray-700">Description</label>
          <textarea
            v-model="form.description"
            rows="3"
            class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
            placeholder="Optional description"
          />
        </div>
        <div class="flex justify-end gap-2">
          <Button type="button" variant="secondary" @click="$emit('update:modelValue', false)">
            Cancel
          </Button>
          <Button type="submit" :loading="props.loading">Create</Button>
        </div>
        <p v-if="error" class="text-sm text-red-500">{{ error }}</p>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import Input from '../ui/Input.vue';
import Button from '../ui/Button.vue';

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  loading: { type: Boolean, default: false },
});

const emit = defineEmits(['update:modelValue', 'submit']);

const form = ref({ name: '', description: '' });
const error = ref('');

watch(
  () => props.modelValue,
  (open) => {
    if (open) {
      form.value = { name: '', description: '' };
      error.value = '';
    }
  }
);

function submit() {
  if (!form.value.name.trim()) return;
  error.value = '';
  emit('submit', { ...form.value });
}
</script>
