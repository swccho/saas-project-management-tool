<template>
  <div v-if="isEmpty && isProjectFormat" class="rounded-lg border border-dashed border-gray-300 bg-gray-50 p-8 text-center text-sm text-gray-500">
    {{ emptyMessage }}
  </div>
  <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <Card v-for="stat in stats" :key="stat.key">
      <CardContent class="pt-6">
        <p class="text-sm font-medium text-gray-500">{{ stat.label }}</p>
        <p class="text-2xl font-semibold" :class="stat.highlight && 'text-red-600'">
          {{ stat.value }}
        </p>
        <div v-if="stat.progress !== undefined" class="mt-2">
          <div class="h-1.5 w-full overflow-hidden rounded-full bg-gray-200">
            <div
              class="h-full rounded-full bg-indigo-600 transition-all"
              :style="{ width: `${Math.min(stat.progress, 100)}%` }"
            />
          </div>
        </div>
      </CardContent>
    </Card>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import Card from '../ui/Card.vue';
import CardContent from '../ui/CardContent.vue';

const props = defineProps({
  analytics: { type: Object, default: () => ({}) },
  loading: { type: Boolean, default: false },
});

const isProjectFormat = computed(() => 'columns' in (props.analytics ?? {}));

const isEmpty = computed(() => {
  if (!isProjectFormat.value) return false;
  const cols = props.analytics?.columns ?? [];
  return !cols.length;
});

const emptyMessage = computed(() => {
  if (props.loading) return 'Loading...';
  return 'No board or columns configured for this project.';
});

const stats = computed(() => {
  const a = props.analytics ?? {};
  const hasColumns = Array.isArray(a.columns) && a.columns.length > 0;

  if (hasColumns) {
    return [
      { key: 'total', label: 'Total tasks', value: a.total_tasks ?? 0, highlight: false },
      ...a.columns.map((col) => ({
        key: `col-${col.id}`,
        label: col.name,
        value: col.count ?? 0,
        highlight: false,
      })),
      { key: 'overdue', label: 'Overdue', value: a.overdue_tasks ?? 0, highlight: (a.overdue_tasks ?? 0) > 0 },
      { key: 'assigned', label: 'Assigned to me', value: a.assigned_to_user ?? 0, highlight: false },
    ];
  }

  return [
    { key: 'total', label: 'Total tasks', value: a.total_tasks ?? 0, highlight: false },
    { key: 'completed', label: 'Completed', value: a.completed_tasks ?? 0, highlight: false },
    { key: 'in_progress', label: 'In progress', value: a.in_progress_tasks ?? 0, highlight: false },
    { key: 'overdue', label: 'Overdue', value: a.overdue_tasks ?? 0, highlight: (a.overdue_tasks ?? 0) > 0 },
    { key: 'assigned', label: 'Assigned to me', value: a.assigned_to_user ?? 0, highlight: false },
    {
      key: 'rate',
      label: 'Completion rate',
      value: `${a.completion_rate ?? 0}%`,
      progress: a.completion_rate ?? 0,
      highlight: false,
    },
  ];
});
</script>
