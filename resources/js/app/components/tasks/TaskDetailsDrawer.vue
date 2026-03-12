<template>
  <Teleport to="body">
    <div
      v-if="modelValue"
      class="fixed inset-0 z-50"
      @click.self="$emit('update:modelValue', false)"
    >
      <div class="fixed inset-0 bg-black/50" />
      <div
        class="fixed right-0 top-0 h-full w-full max-w-lg overflow-y-auto bg-white shadow-xl sm:max-w-md"
        role="dialog"
      >
        <div v-if="loading" class="flex h-full items-center justify-center p-8">
          <div class="h-8 w-8 animate-spin rounded-full border-2 border-indigo-600 border-t-transparent" />
        </div>
        <template v-else-if="task">
          <div class="sticky top-0 flex items-center justify-between border-b border-gray-200 bg-white p-4">
            <h2 class="text-lg font-semibold">Task details</h2>
            <button
              type="button"
              class="rounded p-1 hover:bg-gray-100"
              @click="$emit('update:modelValue', false)"
            >
              ×
            </button>
          </div>
          <div class="space-y-6 p-6">
            <div>
              <h3 class="text-xl font-semibold text-gray-900">{{ task.title }}</h3>
              <p class="mt-1 text-sm text-gray-500">#{{ task.task_number }}</p>
            </div>
            <div v-if="task.description">
              <h4 class="text-sm font-medium text-gray-700">Description</h4>
              <p class="mt-1 text-sm text-gray-600 whitespace-pre-wrap">{{ task.description }}</p>
            </div>
            <div class="space-y-2">
              <div v-if="showAssigneeSelector">
                <AssigneeSelector
                  :model-value="task.assigned_to"
                  :members="projectMembers"
                  @update:model-value="updateAssignee"
                />
              </div>
              <div v-else class="flex flex-wrap items-center gap-2">
                <span class="text-xs text-gray-500">Assignee:</span>
                <template v-if="task.assignee">
                  <Avatar :name="task.assignee?.name" size="sm" />
                  <span class="text-sm">{{ task.assignee?.name }}</span>
                </template>
                <button
                  type="button"
                  class="text-sm text-indigo-600 hover:underline"
                  @click="showAssigneeSelector = true"
                >
                  {{ task.assignee ? 'Change' : '+ Assign' }}
                </button>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-xs text-gray-500">Due:</span>
                <input
                  v-if="editingMeta === 'due_date'"
                  v-model="metaDueDate"
                  type="date"
                  class="rounded border px-2 py-1 text-sm"
                  @blur="saveMeta"
                  @keydown.enter="saveMeta"
                />
                <template v-else>
                  <span v-if="task.due_date" class="text-sm">{{ formatDate(task.due_date) }}</span>
                  <button
                    type="button"
                    class="text-sm text-indigo-600 hover:underline"
                    @click="startEditMeta('due_date')"
                  >
                    {{ task.due_date ? 'Change' : '+ Set due date' }}
                  </button>
                </template>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-xs text-gray-500">Priority:</span>
                <select
                  v-if="editingMeta === 'priority'"
                  v-model="metaPriority"
                  class="rounded border px-2 py-1 text-sm"
                  @change="saveMeta"
                >
                  <option value="">None</option>
                  <option value="low">Low</option>
                  <option value="medium">Medium</option>
                  <option value="high">High</option>
                  <option value="urgent">Urgent</option>
                </select>
                <template v-else>
                  <span v-if="task.priority" class="text-sm capitalize">{{ task.priority }}</span>
                  <button
                    type="button"
                    class="text-sm text-indigo-600 hover:underline"
                    @click="startEditMeta('priority')"
                  >
                    {{ task.priority ? 'Change' : '+ Set priority' }}
                  </button>
                </template>
              </div>
              <div class="flex flex-wrap items-center gap-2">
                <span class="w-full text-xs text-gray-500">Labels:</span>
                <span
                  v-for="l in (task.labels ?? [])"
                  :key="l.id"
                  class="rounded px-2 py-0.5 text-xs"
                  :style="{ backgroundColor: (l.color || '#6366F1') + '20', color: l.color || '#6366F1' }"
                >
                  {{ l.name }}
                </span>
                <select
                  v-if="labels.length > 0"
                  class="rounded border px-2 py-1 text-sm"
                  @change="addLabel($event.target.value)"
                >
                  <option value="">+ Add label</option>
                  <option
                    v-for="l in labels.filter((x) => !(task.labels ?? []).some((t) => t.id === x.id))"
                    :key="l.id"
                    :value="l.id"
                  >
                    {{ l.name }}
                  </option>
                </select>
              </div>
              <div class="space-y-2">
                <h4 class="text-sm font-medium text-gray-700">Subtasks</h4>
                <div v-if="(task.subtasks ?? []).length > 0" class="space-y-1">
                  <div
                    v-for="s in task.subtasks"
                    :key="s.id"
                    class="flex items-center gap-2"
                  >
                    <input
                      type="checkbox"
                      :checked="s.is_completed"
                      @change="toggleSubtask(s)"
                    />
                    <span
                      :class="['text-sm', s.is_completed && 'line-through text-gray-500']"
                    >
                      {{ s.title }}
                    </span>
                  </div>
                </div>
                <form class="flex gap-2" @submit.prevent="addSubtask">
                  <input
                    v-model="newSubtaskTitle"
                    type="text"
                    placeholder="Add subtask..."
                    class="flex-1 rounded border px-2 py-1 text-sm"
                  />
                  <Button size="sm" type="submit">Add</Button>
                </form>
              </div>
              <div v-if="activities.length > 0" class="space-y-2">
                <h4 class="text-sm font-medium text-gray-700">Activity</h4>
                <div class="space-y-6">
                  <div
                    v-for="a in activities"
                    :key="a.id"
                    class="flex gap-3"
                  >
                    <Avatar :name="a.user?.name" size="sm" />
                    <div class="flex-1 space-y-0.5">
                      <p class="text-sm text-gray-900">{{ a.message }}</p>
                      <p class="text-xs text-gray-500">{{ formatActivityTime(a.created_at) }}</p>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="text-sm text-gray-500">No activity yet.</div>
              <div class="flex items-center gap-2">
                <span class="text-xs text-gray-500">Status:</span>
                <select
                  v-if="editingMeta === 'status'"
                  v-model="metaStatus"
                  class="rounded border px-2 py-1 text-sm"
                  @change="saveMeta"
                >
                  <option value="">None</option>
                  <option value="todo">Todo</option>
                  <option value="in_progress">In Progress</option>
                  <option value="blocked">Blocked</option>
                  <option value="done">Done</option>
                </select>
                <template v-else>
                  <span v-if="task.status" class="text-sm">{{ formatStatus(task.status) }}</span>
                  <button
                    type="button"
                    class="text-sm text-indigo-600 hover:underline"
                    @click="startEditMeta('status')"
                  >
                    {{ task.status ? 'Change' : '+ Set status' }}
                  </button>
                </template>
              </div>
            </div>
          </div>
        </template>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { watch, ref } from 'vue';
import Avatar from '../ui/Avatar.vue';
import Button from '../ui/Button.vue';
import AssigneeSelector from './AssigneeSelector.vue';

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  taskId: { type: [Number, String], default: null },
  fetchTask: { type: Function, default: null },
  projectMembers: { type: Array, default: () => [] },
  setAssignee: { type: Function, default: null },
  updateMeta: { type: Function, default: null },
  labels: { type: Array, default: () => [] },
  setLabels: { type: Function, default: null },
  addSubtask: { type: Function, default: null },
  toggleSubtask: { type: Function, default: null },
});

const emit = defineEmits(['update:modelValue']);

const task = ref(null);
const loading = ref(false);
const showAssigneeSelector = ref(false);
const editingMeta = ref(null);
const metaDueDate = ref('');
const metaPriority = ref('');
const metaStatus = ref('');
const newSubtaskTitle = ref('');
const activities = ref([]);

function formatDate(d) {
  if (!d) return '';
  return new Date(d).toLocaleDateString(undefined, {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  });
}

function formatActivityTime(d) {
  if (!d) return '';
  const date = new Date(d);
  const now = new Date();
  const diff = now - date;
  if (diff < 60000) return 'Just now';
  if (diff < 3600000) return `${Math.floor(diff / 60000)}m ago`;
  if (diff < 86400000) return `${Math.floor(diff / 3600000)}h ago`;
  return date.toLocaleDateString(undefined, {
    month: 'short',
    day: 'numeric',
    year: date.getFullYear() !== now.getFullYear() ? 'numeric' : undefined,
  });
}

async function updateAssignee(userId) {
  if (!props.setAssignee || !task.value) return;
  try {
    await props.setAssignee(task.value.id, userId);
    task.value.assigned_to = userId;
    task.value.assignee = userId
      ? props.projectMembers.find((m) => m.user_id === userId)?.user
      : null;
    showAssigneeSelector.value = false;
  } catch {
    // Handle error
  }
}

function startEditMeta(field) {
  editingMeta.value = field;
  metaDueDate.value = task.value?.due_date ?? '';
  metaPriority.value = task.value?.priority ?? '';
  metaStatus.value = task.value?.status ?? '';
}

function formatStatus(s) {
  return s?.replace(/_/g, ' ') ?? '';
}

async function addLabel(labelId) {
  if (!labelId || !props.setLabels || !task.value) return;
  const current = (task.value.labels ?? []).map((l) => l.id);
  if (current.includes(Number(labelId))) return;
  try {
    await props.setLabels(task.value.id, [...current, Number(labelId)]);
    const added = props.labels.find((l) => l.id === Number(labelId));
    if (added) {
      task.value.labels = [...(task.value.labels ?? []), added];
    }
  } catch {
    // Handle error
  }
}

async function addSubtask() {
  if (!newSubtaskTitle.value.trim() || !props.addSubtask || !task.value) return;
  const title = newSubtaskTitle.value.trim();
  newSubtaskTitle.value = '';
  try {
    const data = await props.addSubtask(task.value.id, title);
    task.value.subtasks = [...(task.value.subtasks ?? []), data ?? { id: Date.now(), title, is_completed: false, sort_order: (task.value.subtasks ?? []).length }];
  } catch {
    // Handle error
  }
}

async function toggleSubtask(s) {
  if (!props.toggleSubtask || !task.value) return;
  try {
    await props.toggleSubtask(task.value.id, s.id, !s.is_completed);
    s.is_completed = !s.is_completed;
  } catch {
    // Handle error
  }
}

async function saveMeta() {
  if (!props.updateMeta || !task.value) return;
  try {
    const payload = {};
    if (editingMeta.value === 'due_date') payload.due_date = metaDueDate.value || null;
    if (editingMeta.value === 'priority') payload.priority = metaPriority.value || null;
    if (editingMeta.value === 'status') payload.status = metaStatus.value || null;
    await props.updateMeta(task.value.id, payload);
    if (payload.due_date !== undefined) task.value.due_date = payload.due_date;
    if (payload.priority !== undefined) task.value.priority = payload.priority;
    if (payload.status !== undefined) task.value.status = payload.status;
    editingMeta.value = null;
  } catch {
    // Handle error
  }
}

watch(
  () => [props.modelValue, props.taskId],
  async ([open, id]) => {
    showAssigneeSelector.value = false;
    editingMeta.value = null;
    if (!open || !id || !props.fetchTask) {
      task.value = null;
      activities.value = [];
      return;
    }
    loading.value = true;
    try {
      task.value = await props.fetchTask(id);
      activities.value = props.activities ?? [];
    } catch {
      task.value = null;
      activities.value = [];
    } finally {
      loading.value = false;
    }
  },
  { immediate: true }
);

watch(() => props.activities, (val) => {
  activities.value = val ?? [];
});
</script>
