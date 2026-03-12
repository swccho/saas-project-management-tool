<template>
  <div
    class="cursor-pointer rounded-lg border border-gray-200 bg-white p-3 shadow-sm transition-shadow hover:shadow-md"
    @click="$emit('click', task)"
  >
    <p class="text-sm font-medium text-gray-900">{{ task.title }}</p>
    <div class="mt-2 flex items-center justify-between gap-2">
      <span class="text-xs text-gray-500">{{ taskKey }}</span>
      <Avatar v-if="task.assignee" :name="task.assignee?.name" size="sm" />
    </div>
    <div v-if="(task.labels?.length ?? 0) > 0" class="mt-2 flex flex-wrap gap-1">
      <span
        v-for="l in task.labels"
        :key="l.id"
        class="rounded px-1.5 py-0.5 text-xs"
        :style="{ backgroundColor: (l.color || '#6366F1') + '20', color: l.color || '#6366F1' }"
      >
        {{ l.name }}
      </span>
    </div>
    <div v-if="task.due_date || task.priority" class="mt-2 flex flex-wrap gap-1">
      <span
        v-if="task.due_date"
        :class="[
          'rounded px-1.5 py-0.5 text-xs',
          isOverdue ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600',
        ]"
      >
        {{ formatDate(task.due_date) }}
      </span>
      <span
        v-if="task.priority"
        class="rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-600"
      >
        {{ task.priority }}
      </span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import Avatar from '../ui/Avatar.vue';

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
