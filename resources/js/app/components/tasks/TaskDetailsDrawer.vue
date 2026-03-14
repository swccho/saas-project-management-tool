<template>
  <Teleport v-if="modelValue" to="body">
    <div
      class="fixed inset-0 z-50 flex justify-end"
      @click.self="$emit('update:modelValue', false)"
    >
      <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" />
      <div
        class="relative flex h-full w-full max-h-full flex-col bg-white shadow-2xl transition-transform sm:w-full sm:max-w-lg"
        role="dialog"
        aria-labelledby="task-details-title"
      >
        <div v-if="loading" class="flex flex-1 items-center justify-center p-12">
          <div class="h-8 w-8 animate-spin rounded-full border-2 border-indigo-600 border-t-transparent" />
        </div>
        <template v-else-if="task">
          <!-- Header -->
          <div class="flex shrink-0 items-center justify-between border-b border-gray-100 bg-white px-6 py-4">
            <h2 id="task-details-title" class="text-sm font-medium text-gray-500">Task details</h2>
            <div class="flex items-center gap-2">
              <button
                v-if="props.setWatch && props.unsetWatch"
                type="button"
                class="flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm transition-colors hover:bg-gray-50"
                :class="task.is_watching && 'border-indigo-200 bg-indigo-50 text-indigo-700'"
                @click="toggleWatch"
              >
                <Eye v-if="task.is_watching" class="h-4 w-4" />
                <EyeOff v-else class="h-4 w-4" />
                {{ task.is_watching ? 'Watching' : 'Watch' }}
                <span v-if="task.watchers_count > 0" class="rounded-full bg-gray-200 px-1.5 py-0.5 text-xs">{{ task.watchers_count }}</span>
              </button>
              <button
                type="button"
                class="rounded-lg p-2 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600"
                aria-label="Close"
                @click="$emit('update:modelValue', false)"
              >
                <X class="h-5 w-5" />
              </button>
            </div>
          </div>

          <!-- Scrollable content -->
          <div class="flex-1 overflow-y-auto">
            <div class="space-y-6 p-6">
              <!-- Task title hero -->
              <div class="space-y-1">
                <h3 class="text-xl font-semibold tracking-tight text-gray-900">{{ task.title }}</h3>
                <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">
                  #{{ task.task_number }}
                </span>
              </div>

              <!-- Metadata grid -->
              <div class="grid gap-3 sm:grid-cols-2">
                <!-- Assignee -->
                <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4">
                  <div v-if="showAssigneeSelector">
                    <AssigneeSelector
                      :model-value="task.assigned_to"
                      :members="projectMembers"
                      @update:model-value="updateAssignee"
                    />
                  </div>
                  <div v-else class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white shadow-sm">
                      <User class="h-4 w-4 text-gray-500" />
                    </div>
                    <div class="min-w-0 flex-1">
                      <p class="text-xs font-medium text-gray-500">Assignee</p>
                      <div class="mt-0.5 flex items-center gap-2">
                        <template v-if="task.assignee">
                          <Avatar :name="task.assignee?.name" :src="task.assignee?.avatar_url" :status="presenceStatus(task.assignee?.id)" size="sm" />
                          <span class="truncate text-sm font-medium text-gray-900">{{ task.assignee?.name }}</span>
                        </template>
                        <button
                          type="button"
                          class="text-sm font-medium text-indigo-600 hover:text-indigo-700"
                          @click="showAssigneeSelector = true"
                        >
                          {{ task.assignee ? 'Change' : 'Assign' }}
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Due date -->
                <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4">
                  <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white shadow-sm">
                      <Calendar class="h-4 w-4 text-gray-500" />
                    </div>
                    <div class="min-w-0 flex-1">
                      <p class="text-xs font-medium text-gray-500">Due date</p>
                      <div class="mt-0.5">
                        <input
                          v-if="editingMeta === 'due_date'"
                          v-model="metaDueDate"
                          type="date"
                          class="w-full rounded-lg border border-gray-200 px-2 py-1.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                          @blur="saveMeta"
                          @keydown.enter="saveMeta"
                        />
                        <button
                          v-else
                          type="button"
                          class="text-left text-sm font-medium"
                          :class="task.due_date ? 'text-gray-900' : 'text-indigo-600 hover:text-indigo-700'"
                          @click="startEditMeta('due_date')"
                        >
                          {{ task.due_date ? formatDate(task.due_date) : 'Set due date' }}
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Priority -->
                <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4">
                  <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white shadow-sm">
                      <Flag class="h-4 w-4 text-gray-500" />
                    </div>
                    <div class="min-w-0 flex-1">
                      <p class="text-xs font-medium text-gray-500">Priority</p>
                      <div class="mt-0.5">
                        <select
                          v-if="editingMeta === 'priority'"
                          v-model="metaPriority"
                          class="w-full rounded-lg border border-gray-200 px-2 py-1.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                          @change="saveMeta"
                        >
                          <option value="">None</option>
                          <option value="low">Low</option>
                          <option value="medium">Medium</option>
                          <option value="high">High</option>
                          <option value="urgent">Urgent</option>
                        </select>
                        <button
                          v-else
                          type="button"
                          class="text-left text-sm font-medium"
                          :class="task.priority ? 'text-gray-900' : 'text-indigo-600 hover:text-indigo-700'"
                          @click="startEditMeta('priority')"
                        >
                          {{ task.priority ? task.priority.charAt(0).toUpperCase() + task.priority.slice(1) : 'Set priority' }}
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

              </div>

              <!-- Labels -->
              <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4">
                <div class="flex items-start gap-3">
                  <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white shadow-sm">
                    <Tag class="h-4 w-4 text-gray-500" />
                  </div>
                  <div class="min-w-0 flex-1">
                    <p class="text-xs font-medium text-gray-500">Labels</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                      <span
                        v-for="l in (task.labels ?? [])"
                        :key="l.id"
                        class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-medium"
                        :style="{ backgroundColor: (l.color || '#6366F1') + '20', color: l.color || '#6366F1' }"
                      >
                        {{ l.name }}
                      </span>
                      <select
                        v-if="labels.length > 0"
                        class="rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-sm text-gray-600 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
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
                  </div>
                </div>
              </div>

              <!-- Description -->
              <div v-if="task.description" class="rounded-xl border border-gray-100 bg-gray-50/50 p-4">
                <h4 class="text-xs font-medium uppercase tracking-wider text-gray-500">Description</h4>
                <p class="mt-3 whitespace-pre-wrap text-sm leading-relaxed text-gray-700">{{ task.description }}</p>
              </div>

              <!-- Subtasks -->
              <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4">
                <h4 class="text-xs font-medium uppercase tracking-wider text-gray-500">Subtasks</h4>
                <div v-if="(task.subtasks ?? []).length > 0" class="mt-3 space-y-2">
                  <div
                    v-for="s in task.subtasks"
                    :key="s.id"
                    class="flex items-center gap-3 rounded-lg bg-white px-3 py-2 shadow-sm transition-colors hover:bg-gray-50"
                  >
                    <input
                      type="checkbox"
                      :checked="s.is_completed"
                      class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                      @change="toggleSubtask(s)"
                    />
                    <span
                      :class="['flex-1 text-sm', s.is_completed && 'text-gray-500 line-through']"
                    >
                      {{ s.title }}
                    </span>
                  </div>
                </div>
                <form class="mt-3 flex gap-2" @submit.prevent="addSubtask">
                  <input
                    v-model="newSubtaskTitle"
                    type="text"
                    placeholder="Add subtask..."
                    class="flex-1 rounded-lg border border-gray-200 px-3 py-2 text-sm placeholder:text-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                  />
                  <Button size="sm" type="submit">Add</Button>
                </form>
              </div>

              <!-- Attachments -->
              <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4">
                <TaskAttachmentsSection
                  :attachments="attachments"
                  :loading="attachmentsLoading"
                  :upload-attachment="uploadAttachment"
                  :delete-attachment="deleteAttachment"
                  :download-attachment="downloadAttachment"
                />
              </div>

              <!-- Comments -->
              <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4">
                <TaskCommentsSection
                  :comments="comments"
                  :loading="commentsLoading"
                  :members="projectMembers"
                  :add-comment="addComment"
                  :update-comment="updateComment"
                  :delete-comment="deleteComment"
                />
              </div>

              <!-- Activity -->
              <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4">
                <h4 class="text-xs font-medium uppercase tracking-wider text-gray-500">Activity</h4>
                <div v-if="activities.length > 0" class="mt-3 space-y-4">
                  <div
                    v-for="a in activities"
                    :key="a.id"
                    class="flex gap-3"
                  >
                    <Avatar :name="a.user?.name" :status="presenceStatus(a.user?.id)" size="sm" class="shrink-0" />
                    <div class="min-w-0 flex-1">
                      <p class="text-sm text-gray-900">{{ a.message }}</p>
                      <p class="mt-0.5 text-xs text-gray-500">{{ formatActivityTime(a.created_at) }}</p>
                    </div>
                  </div>
                </div>
                <EmptyState
                  v-else
                  title="No activity yet"
                  :compact="true"
                  :icon="Activity"
                />
              </div>
            </div>
          </div>
        </template>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { watch, ref, onMounted, onUnmounted } from 'vue';
import { Activity, Calendar, Eye, EyeOff, Flag, Tag, User, X } from 'lucide-vue-next';
import Avatar from '../ui/Avatar.vue';
import EmptyState from '../shared/EmptyState.vue';
import { usePresenceStore } from '../../stores/presenceStore';

const presenceStore = usePresenceStore();

function presenceStatus(userId) {
  return userId ? presenceStore.getStatus(userId) : '';
}
import Button from '../ui/Button.vue';
import AssigneeSelector from './AssigneeSelector.vue';
import TaskAttachmentsSection from './TaskAttachmentsSection.vue';
import TaskCommentsSection from './TaskCommentsSection.vue';

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
  comments: { type: Array, default: () => [] },
  commentsLoading: { type: Boolean, default: false },
  attachments: { type: Array, default: () => [] },
  attachmentsLoading: { type: Boolean, default: false },
  uploadAttachment: { type: Function, default: null },
  deleteAttachment: { type: Function, default: null },
  downloadAttachment: { type: Function, default: null },
  addComment: { type: Function, default: null },
  updateComment: { type: Function, default: null },
  deleteComment: { type: Function, default: null },
  setWatch: { type: Function, default: null },
  unsetWatch: { type: Function, default: null },
});

const emit = defineEmits(['update:modelValue']);

const task = ref(null);
const loading = ref(false);
const showAssigneeSelector = ref(false);
const editingMeta = ref(null);
const metaDueDate = ref('');
const metaPriority = ref('');
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

async function toggleWatch() {
  if (!task.value || !props.setWatch || !props.unsetWatch) return;
  try {
    if (task.value.is_watching) {
      await props.unsetWatch(task.value.id);
      task.value.is_watching = false;
      task.value.watchers_count = Math.max(0, (task.value.watchers_count ?? 0) - 1);
    } else {
      await props.setWatch(task.value.id);
      task.value.is_watching = true;
      task.value.watchers_count = (task.value.watchers_count ?? 0) + 1;
    }
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
    await props.updateMeta(task.value.id, payload);
    if (payload.due_date !== undefined) task.value.due_date = payload.due_date;
    if (payload.priority !== undefined) task.value.priority = payload.priority;
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

function handleKeydown(e) {
  if (e.key === 'Escape' && props.modelValue) {
    emit('update:modelValue', false);
  }
}

onMounted(() => document.addEventListener('keydown', handleKeydown));
onUnmounted(() => document.removeEventListener('keydown', handleKeydown));
</script>
