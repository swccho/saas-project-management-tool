<template>
  <div
    class="h-fit min-w-[240px] flex-shrink-0 self-start rounded-xl border-2 p-4 transition-colors sm:min-w-[280px]"
    :class="[
      isDragOver
        ? 'border-indigo-400 bg-indigo-50/80'
        : 'border-gray-200 bg-gray-50/50',
    ]"
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
        :list="localTasks"
        :data-column-id="column.id"
        :disabled="!allowTaskDrag"
        :group="{ name: 'tasks', pull: true, put: true }"
        :empty-insert-threshold="120"
        item-key="id"
        tag="div"
        :class="[
          'space-y-2',
          (localTasks?.length ?? 0) === 0 && !isDragOver ? 'min-h-0' : 'min-h-[80px]',
        ]"
        ghost-class="task-drag-ghost"
        chosen-class="shadow-md"
        drag-class="shadow-lg"
        @end="onDragEnd"
        @start="onDragStart"
        @change="onListChange"
        @add="onAdd"
        @move="onDragMove"
      >
        <TaskCard
          v-for="task in localTasks"
          :key="task.id"
          :task="task"
          class="transition-shadow hover:shadow-md"
          @click="$emit('task-click', task)"
        />
      </draggable>
      <div
        v-if="(localTasks?.length ?? 0) === 0"
        class="rounded-lg border-2 border-dashed border-gray-200 py-4 text-center text-sm text-gray-500"
        :class="isDragOver ? 'block' : 'hidden'"
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
import { ref, watch, nextTick } from 'vue';
import { VueDraggableNext as draggable } from 'vue-draggable-next';
import TaskCard from '../tasks/TaskCard.vue';

const props = defineProps({
  column: { type: Object, required: true },
  tasks: { type: Array, default: () => [] },
  allowTaskDrag: { type: Boolean, default: true },
  isDragOver: { type: Boolean, default: false },
});

const emit = defineEmits([
  'update:tasks',
  'task-click',
  'add-task',
  'delete-column',
  'task-moved',
  'column-rename',
  'drag-over',
  'drag-end',
]);

const localTasks = ref([]);

watch(
  () => props.tasks,
  (val) => {
    const arr = Array.isArray(val) ? [...val] : [];
    localTasks.value.splice(0, localTasks.value.length, ...arr);
  },
  { immediate: true }
);

const moveEmittedForTask = ref(null);

function emitTaskMoved(task, sortOrder) {
  if (task?.id == null || moveEmittedForTask.value === task.id) return;
  moveEmittedForTask.value = task.id;
  emit('task-moved', { task, columnId: Number(props.column.id), sortOrder });
  setTimeout(() => { moveEmittedForTask.value = null; }, 500);
}

function onListChange(evt) {
  emit('update:tasks', [...localTasks.value]);
  if (!evt) return;
  if (evt.added) {
    const newIndex = evt.added.newIndex ?? 0;
    const task = localTasks.value[newIndex] ?? evt.added.element;
    emitTaskMoved(task, newIndex);
  } else if (evt.moved) {
    const task = localTasks.value[evt.moved.newIndex];
    if (task?.id != null) {
      emit('task-moved', { task, columnId: Number(props.column.id), sortOrder: evt.moved.newIndex });
    }
  }
}

function onAdd(evt) {
  if (!evt || evt.newIndex == null) return;
  nextTick(() => {
    const task = localTasks.value[evt.newIndex];
    emitTaskMoved(task, evt.newIndex);
  });
}

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

function onDragStart() {
  isDragging.value = true;
  emit('drag-over');
}

function onDragMove() {
  emit('drag-over');
  return true;
}

function onDragEnd() {
  isDragging.value = false;
  emit('drag-end');
}

</script>
