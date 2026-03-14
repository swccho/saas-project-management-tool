<template>
  <div class="flex flex-wrap items-center gap-2">
    <AssigneeFilterDropdown
      :model-value="filters.assignee ?? []"
      :project-members="projectMembers"
      @update:model-value="$emit('update:filter', { key: 'assignee', value: $event })"
    />
    <PriorityFilterDropdown
      :model-value="filters.priority ?? []"
      @update:model-value="$emit('update:filter', { key: 'priority', value: $event })"
    />
    <LabelFilterDropdown
      :model-value="filters.label ?? []"
      :labels="labels"
      @update:model-value="$emit('update:filter', { key: 'label', value: $event })"
    />
    <button
      v-if="activeFilterCount > 0"
      type="button"
      class="flex items-center gap-1.5 rounded-md border border-gray-200 bg-white px-2.5 py-1.5 text-sm text-gray-600 shadow-sm transition-colors hover:bg-gray-50 hover:text-gray-900"
      @click="clearFilters"
    >
      <X class="h-4 w-4" />
      Clear
      <span class="rounded-full bg-gray-200 px-1.5 py-0.5 text-xs font-medium text-gray-700">
        {{ activeFilterCount }}
      </span>
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { X } from 'lucide-vue-next';
import AssigneeFilterDropdown from './AssigneeFilterDropdown.vue';
import PriorityFilterDropdown from './PriorityFilterDropdown.vue';
import LabelFilterDropdown from './LabelFilterDropdown.vue';

const props = defineProps({
  filters: { type: Object, required: true },
  projectMembers: { type: Array, default: () => [] },
  labels: { type: Array, default: () => [] },
});

const emit = defineEmits(['clear-filters', 'update:filter']);

const activeFilterCount = computed(() => {
  const f = props.filters;
  let n = 0;
  if ((f.assignee ?? []).length > 0) n++;
  if ((f.priority ?? []).length > 0) n++;
  if ((f.label ?? []).length > 0) n++;
  return n;
});

function clearFilters() {
  emit('clear-filters');
}
</script>
