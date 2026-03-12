<template>
  <div
    class="min-w-[240px] flex-shrink-0 rounded-xl border border-gray-200 bg-gray-50/50 p-4 transition-colors sm:min-w-[280px]"
    :data-column-id="column.id"
  >
    <div class="flex items-center justify-between gap-2">
      <h3
        v-if="!editing"
        class="text-sm font-semibold text-gray-900"
        @dblclick="startEdit"
      >
        {{ column.name }}
      </h3>
      <input
        v-else
        ref="nameInput"
        v-model="editName"
        type="text"
        class="flex-1 rounded border border-indigo-500 px-2 py-1 text-sm"
        @blur="saveEdit"
        @keydown.enter="saveEdit"
      />
      <button
        type="button"
        class="rounded p-1 text-gray-400 hover:bg-gray-200 hover:text-gray-600"
        title="Delete column"
        @click="$emit('delete-column')"
      >
        ×
      </button>
    </div>
    <div class="mt-3 space-y-2">
      <draggable
        v-model="localTasks"
        :data-column-id="column.id"
        :disabled="!allowTaskDrag"
        group="tasks"
        item-key="id"
        tag="div"
        class="space-y-2"
        :class="{ 'opacity-50': isDragging }"
        ghost-class="opacity-50"
        chosen-class="shadow-md"
        drag-class="shadow-lg"
        @end="onDragEnd"
        @start="isDragging = true"
      >
        <template #item="{ element: task }">
          <TaskCard
            :task="task"
            class="transition-shadow hover:shadow-md"
            @click="$emit('task-click', task)"
          />
        </template>
      </draggable>
      <div
        v-if="(localTasks?.length ?? 0) === 0"
        class="rounded-lg border-2 border-dashed border-gray-200 py-4 text-center text-sm text-gray-500"
      >
        No tasks in this column
      </div>
      <button
        type="button"
        class="w-full rounded-lg border-2 border-dashed border-gray-300 py-2 text-sm text-gray-500 hover:border-indigo-400 hover:text-indigo-600"
        @click="$emit('add-task')"
      >
        + Add task
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue';
import { VueDraggableNext as draggable } from 'vue-draggable-next';
import TaskCard from '../tasks/TaskCard.vue';

const props = defineProps({
  column: { type: Object, required: true },
  tasks: { type: Array, default: () => [] },
  allowTaskDrag: { type: Boolean, default: true },
});

const emit = defineEmits([
  'update:tasks',
  'task-click',
  'add-task',
  'delete-column',
  'task-moved',
  'column-rename',
]);

const localTasks = computed({
  get: () => [...(props.tasks || [])],
  set: (val) => emit('update:tasks', val),
});

const editing = ref(false);
const editName = ref('');
const nameInput = ref(null);
const isDragging = ref(false);

function startEdit() {
  editing.value = true;
  editName.value = props.column.name;
  nextTick(() => nameInput.value?.focus());
}

function saveEdit() {
  if (editing.value && editName.value.trim() !== props.column.name) {
    emit('column-rename', editName.value.trim());
  }
  editing.value = false;
}

function onDragEnd(evt) {
  isDragging.value = false;
  if (!evt) return;
  if (evt.newIndex == null || evt.to == null) return;
  const toColEl = evt.to.closest?.('[data-column-id]');
  const toColumnId = toColEl?.getAttribute?.('data-column-id');
  if (!toColumnId) return;
  const task = localTasks.value[evt.newIndex];
  if (!task?.id) return;
  emit('task-moved', {
    task,
    columnId: toColumnId,
    sortOrder: evt.newIndex,
  });
}

</script>
