<template>
  <div
    class="group cursor-pointer rounded-xl border border-gray-100 bg-white p-4 shadow-sm transition-all duration-200 hover:border-gray-200 hover:shadow-md"
    @click="$emit('click', task)"
  >
    <p class="text-sm font-medium leading-snug text-gray-900">{{ task.title }}</p>
    <div class="mt-3 flex items-center justify-between gap-2">
      <span class="text-xs font-medium text-gray-400">{{ taskKey }}</span>
      <Avatar v-if="task.assignee" :name="task.assignee?.name" :src="task.assignee?.avatar_url" size="sm" class="shrink-0" />
    </div>
    <div v-if="(task.labels?.length ?? 0) > 0 || task.priority || task.due_date" class="mt-3 flex flex-wrap items-center gap-1.5">
      <span
        v-for="l in task.labels"
        :key="l.id"
        class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium"
        :style="{ backgroundColor: (l.color || '#6366F1') + '18', color: l.color || '#6366F1' }"
      >
        {{ l.name }}
      </span>
      <span
        v-if="task.priority"
        :class="[
          'inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium capitalize',
          priorityClasses[task.priority] ?? 'bg-gray-100 text-gray-600',
        ]"
      >
        {{ task.priority }}
      </span>
      <span
        v-if="task.due_date"
        :class="[
          'inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium',
          isOverdue ? 'bg-red-50 text-red-600' : 'bg-gray-100 text-gray-600',
        ]"
      >
        {{ formatDate(task.due_date) }}
      </span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import Avatar from '../ui/Avatar.vue';

const priorityClasses = {
  low: 'bg-gray-100 text-gray-600',
  medium: 'bg-blue-50 text-blue-700',
  high: 'bg-amber-50 text-amber-700',
  urgent: 'bg-red-50 text-red-600',
};

const props = defineProps({
  task: { type: Object, required: true },
});

defineEmits(['click']);

const taskKey = computed(() => {
  const p = props.task.project_id;
  const n = props.task.task_number;
  return p && n ? `#${n}` : '';
});

const isOverdue = computed(() => {
  if (!props.task.due_date) return false;
  return new Date(props.task.due_date) < new Date() && props.task.status !== 'done';
});

function formatDate(d) {
  if (!d) return '';
  const date = new Date(d);
  return date.toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' });
}
</script>
