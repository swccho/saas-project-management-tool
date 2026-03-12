<template>
  <div class="space-y-1">
    <label class="block text-sm font-medium text-gray-700">Assignee</label>
    <select
      :value="modelValue"
      class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
      @change="$emit('update:modelValue', $event.target.value ? Number($event.target.value) : null)"
    >
      <option value="">Unassigned</option>
      <option v-for="m in members" :key="m.user_id" :value="m.user_id">
        {{ m.user?.name }}
      </option>
    </select>
  </div>
</template>

<script setup>
defineProps({
  modelValue: { type: [Number, String], default: null },
  members: { type: Array, default: () => [] },
});

defineEmits(['update:modelValue']);
</script>
