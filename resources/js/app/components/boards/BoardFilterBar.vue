<template>
  <div class="flex flex-wrap items-center gap-2">
    <select
      :value="filters.assignee"
      class="rounded-lg border border-gray-300 px-2.5 py-1.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
      @change="$emit('update:filter', { key: 'assignee', value: $event.target.value ? Number($event.target.value) : null })"
    >
      <option value="">All assignees</option>
      <option v-for="m in projectMembers" :key="m.id" :value="m.id">
        {{ m.name }}
      </option>
    </select>
    <select
      :value="filters.priority ?? ''"
      class="rounded-lg border border-gray-300 px-2.5 py-1.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
      @change="$emit('update:filter', { key: 'priority', value: $event.target.value || null })"
    >
      <option value="">All priorities</option>
      <option value="low">Low</option>
      <option value="medium">Medium</option>
      <option value="high">High</option>
      <option value="urgent">Urgent</option>
    </select>
    <select
      :value="filters.label ?? ''"
      class="rounded-lg border border-gray-300 px-2.5 py-1.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
      @change="$emit('update:filter', { key: 'label', value: $event.target.value ? Number($event.target.value) : null })"
    >
      <option value="">All labels</option>
      <option v-for="l in labels" :key="l.id" :value="l.id">
        {{ l.name }}
      </option>
    </select>
    <select
      :value="filters.status ?? ''"
      class="rounded-lg border border-gray-300 px-2.5 py-1.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
      @change="$emit('update:filter', { key: 'status', value: $event.target.value || null })"
    >
      <option value="">All statuses</option>
      <option value="todo">To Do</option>
      <option value="in_progress">In Progress</option>
      <option value="blocked">Blocked</option>
      <option value="done">Done</option>
    </select>
    <label class="flex cursor-pointer items-center gap-1.5 rounded-lg border border-gray-300 px-2.5 py-1.5 text-sm hover:bg-gray-50">
      <input
        :checked="filters.overdueOnly"
        type="checkbox"
        class="rounded border-gray-300"
        @change="$emit('update:filter', { key: 'overdueOnly', value: $event.target.checked })"
      />
      <span>Overdue only</span>
    </label>
    <button
      v-if="activeFilterCount > 0"
      type="button"
      class="rounded-lg border border-gray-300 px-2.5 py-1.5 text-sm text-gray-600 hover:bg-gray-100"
      @click="clearFilters"
    >
      Clear filters
      <span v-if="activeFilterCount > 0" class="ml-1 rounded-full bg-indigo-100 px-1.5 py-0.5 text-xs text-indigo-700">
        {{ activeFilterCount }}
      </span>
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  filters: { type: Object, required: true },
  projectMembers: { type: Array, default: () => [] },
  labels: { type: Array, default: () => [] },
});

const emit = defineEmits(['clear-filters', 'update:filter']);

const activeFilterCount = computed(() => {
  const f = props.filters;
  let n = 0;
  if (f.assignee != null) n++;
  if (f.priority) n++;
  if (f.label != null) n++;
  if (f.status) n++;
  if (f.overdueOnly) n++;
  return n;
});

function clearFilters() {
  emit('clear-filters');
}
</script>
