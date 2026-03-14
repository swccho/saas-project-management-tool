<template>
  <div class="min-w-0 space-y-6">
    <template v-if="!project">
      <p class="text-sm text-gray-600">Loading...</p>
    </template>

    <template v-else-if="boards.length === 0">
      <Card>
        <CardContent class="p-0">
          <EmptyState
            title="No boards yet"
            description="Create your first board to get started."
          >
            <Button @click="showCreateBoard = true">Create board</Button>
          </EmptyState>
        </CardContent>
      </Card>
    </template>

    <template v-else>
      <div class="space-y-4">
        <div class="sticky top-0 z-10 flex flex-wrap items-center justify-between gap-4 rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-sm">
          <div class="flex flex-wrap items-center gap-3">
            <div class="relative flex items-center rounded-md border border-gray-200 bg-white shadow-sm">
              <Search class="absolute left-2.5 h-4 w-4 text-gray-400" />
              <input
                v-model="searchInput"
                type="text"
                placeholder="Search tasks..."
                class="h-9 w-48 border-0 bg-transparent py-1.5 pl-9 pr-8 text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-0"
              />
              <button
                v-if="searchQuery"
                type="button"
                class="absolute right-2 rounded p-0.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                @click="(searchInput = ''), (searchQuery = '')"
              >
                <X class="h-4 w-4" />
              </button>
            </div>
            <div class="h-5 w-px bg-gray-200" />
            <BoardFilterBar
              :filters="boardFilters"
              :project-members="projectMembers"
              :labels="labels"
              @update:filter="({ key, value }) => (boardFilters[key] = value)"
              @clear-filters="clearFilters"
            />
          </div>
          <div class="flex items-center gap-2">
            <BoardSelectorDropdown
              :model-value="selectedBoardId"
              :boards="boards"
              @update:model-value="selectedBoardId = $event"
            />
            <Button size="sm" @click="showCreateBoard = true">New board</Button>
          </div>
        </div>
        <BoardSkeleton v-if="boardLoading" :column-count="4" :card-count="3" />
        <template v-else-if="boardColumns.length === 0 && searchQuery">
          <EmptyColumnState message="No tasks match your search">
            <template #action>
              <button
                type="button"
                class="mt-2 text-sm text-indigo-600 hover:underline"
                @click="(searchInput = ''), (searchQuery = '')"
              >
                Clear search
              </button>
            </template>
          </EmptyColumnState>
        </template>
        <template v-else-if="boardColumns.length === 0 && !hasActiveFilters && !searchQuery">
          <EmptyColumnState message="Add your first column to get started">
            <template #action>
              <Button class="mt-2" size="sm" @click="showAddColumn = true">Add column</Button>
            </template>
          </EmptyColumnState>
        </template>
        <template v-else-if="boardColumns.every((c) => (c.tasks ?? []).length === 0) && hasActiveFilters">
          <EmptyColumnState message="No tasks match filters">
            <template #action>
              <button
                type="button"
                class="mt-2 text-sm text-indigo-600 hover:underline"
                @click="clearFilters"
              >
                Clear filters
              </button>
            </template>
          </EmptyColumnState>
        </template>
         <div
          v-else
          class="sticky bottom-0 flex min-w-0 max-w-full flex-nowrap items-start gap-4 overflow-x-auto overflow-y-hidden bg-[#FAFAFA] pb-4 pt-2"
        >
          <BoardColumn
              v-for="col in boardColumns"
              :key="col.id"
              :column="col"
              :tasks="col.tasks ?? []"
              :allow-task-drag="true"
              :is-drag-over="dragOverColumnId === col.id"
              @update:tasks="(t) => updateColumnTasks(col.id, t)"
              @task-click="openTask"
              @add-task="startAddTask(col)"
              @delete-column="deleteColumn(col)"
              @task-moved="handleTaskMoved"
              @column-rename="(name) => saveColumnName(col, name)"
              @drag-over="dragOverColumnId = col.id"
              @drag-end="dragOverColumnId = null"
            />
          <button
          type="button"
          class="flex h-[120px] min-w-[280px] flex-shrink-0 self-start items-center justify-center rounded-xl border-2 border-dashed border-gray-300 text-sm text-gray-500 hover:border-indigo-400 hover:text-indigo-600"
          @click="showAddColumn = true"
        >
            + Add column
          </button>
        </div>
      </div>
    </template>

    <div
      v-if="showCreateBoard"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
      @click.self="showCreateBoard = false"
    >
      <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
        <h3 class="text-lg font-semibold">Create board</h3>
        <form class="mt-4 space-y-4" @submit.prevent="createBoard">
          <Input v-model="newBoardName" label="Name" placeholder="Development Board" required />
          <div class="flex justify-end gap-2">
            <Button type="button" variant="secondary" @click="showCreateBoard = false">
              Cancel
            </Button>
            <Button type="submit" :loading="creating">Create</Button>
          </div>
        </form>
      </div>
    </div>

    <div
      v-if="showAddTask"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
      @click.self="showAddTask = false"
    >
      <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
        <h3 class="text-lg font-semibold">Add task</h3>
        <p class="mt-1 text-sm text-gray-600">in {{ addTaskColumn?.name }}</p>
        <form class="mt-4 space-y-4" @submit.prevent="addTask">
          <Input v-model="newTaskTitle" label="Title" placeholder="Task title" required />
          <div class="flex justify-end gap-2">
            <Button type="button" variant="secondary" @click="showAddTask = false">
              Cancel
            </Button>
            <Button type="submit" :loading="addingTask">Add</Button>
          </div>
        </form>
      </div>
    </div>

    <div
      v-if="showAddColumn"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
      @click.self="showAddColumn = false"
    >
      <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
        <h3 class="text-lg font-semibold">Add column</h3>
        <form class="mt-4 space-y-4" @submit.prevent="addColumn">
          <Input v-model="newColumnName" label="Name" placeholder="New column" required />
          <div class="flex justify-end gap-2">
            <Button type="button" variant="secondary" @click="showAddColumn = false">
              Cancel
            </Button>
            <Button type="submit" :loading="addingColumn">Add</Button>
          </div>
        </form>
      </div>
    </div>

    <ConfirmModal
      v-model="showDeleteColumnModal"
      title="Delete column"
      :message="deleteColumnTarget ? `Delete column '${deleteColumnTarget.name}'?` : ''"
      confirm-label="Delete"
      confirm-variant="destructive"
      @confirm="confirmDeleteColumn"
    />

    <TaskDetailsDrawer
      v-model="showTaskDrawer"
      :task-id="selectedTaskId"
      :fetch-task="fetchTaskById"
      :project-members="projectMembers"
      :set-assignee="setAssignee"
      :update-meta="updateTaskMeta"
      :labels="labels"
      :set-labels="setTaskLabels"
      :add-subtask="addSubtaskToTask"
      :toggle-subtask="toggleSubtaskComplete"
      :activities="drawerActivities"
      :attachments="drawerAttachments"
      :attachments-loading="drawerAttachmentsLoading"
      :upload-attachment="uploadAttachmentToTask"
      :delete-attachment="deleteTaskAttachment"
      :download-attachment="downloadTaskAttachment"
      :comments="drawerComments"
      :comments-loading="drawerCommentsLoading"
      :add-comment="addCommentToTask"
      :update-comment="updateTaskComment"
      :delete-comment="deleteTaskComment"
      :set-watch="setTaskWatch"
      :unset-watch="unsetTaskWatch"
    />
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue';
import { useDebounceFn } from '@vueuse/core';
import { useRoute } from 'vue-router';
import { useWorkspaceStore } from '../../stores/workspaceStore';
import { projectService } from '../../services/projectService';
import { boardService } from '../../services/boardService';
import { boardColumnService } from '../../services/boardColumnService';
import { taskService } from '../../services/taskService';
import { projectMemberService } from '../../services/projectMemberService';
import { labelService } from '../../services/labelService';
import { subtaskService } from '../../services/subtaskService';
import { taskActivityService } from '../../services/taskActivityService';
import { commentService } from '../../services/commentService';
import { attachmentService } from '../../services/attachmentService';
import { useBoardFilters } from '../../composables/useBoardFilters';
import { useBoardSort } from '../../composables/useBoardSort';
import Button from '../../components/ui/Button.vue';
import BoardColumn from '../../components/boards/BoardColumn.vue';
import BoardFilterBar from '../../components/boards/BoardFilterBar.vue';
import BoardSelectorDropdown from '../../components/boards/BoardSelectorDropdown.vue';
import BoardSkeleton from '../../components/boards/BoardSkeleton.vue';
import EmptyColumnState from '../../components/boards/EmptyColumnState.vue';
import EmptyState from '../../components/shared/EmptyState.vue';
import TaskDetailsDrawer from '../../components/tasks/TaskDetailsDrawer.vue';
import Card from '../../components/ui/Card.vue';
import CardContent from '../../components/ui/CardContent.vue';
import ConfirmModal from '../../components/ui/ConfirmModal.vue';
import Input from '../../components/ui/Input.vue';
import { Search, X } from 'lucide-vue-next';

const route = useRoute();
const workspaceStore = useWorkspaceStore();
const project = ref(null);
const boards = ref([]);
const selectedBoardId = ref(null);
const showCreateBoard = ref(false);
const showAddColumn = ref(false);
const newBoardName = ref('');
const newColumnName = ref('');
const creating = ref(false);
const addingColumn = ref(false);
const columnsWithTasks = ref([]);
const movingTask = ref(false);
const reorderingColumns = ref(false);
const dragOverColumnId = ref(null);
const showAddTask = ref(false);
const addTaskColumn = ref(null);
const newTaskTitle = ref('');
const addingTask = ref(false);
const projectMembers = ref([]);
const labels = ref([]);
const taskActivities = ref([]);

const projectId = computed(() => route.params.id);

const { boardFilters, clearFilters, applyFilters } = useBoardFilters();
const { sortMode, applySort } = useBoardSort();
const filteredColumnsWithTasks = ref([]);
const displayColumns = ref([]);
const boardColumns = ref([]);
const searchInput = ref('');
const searchQuery = ref('');
const boardLoading = ref(false);

const currentBoard = computed(() =>
  boards.value.find((b) => Number(b.id) === Number(selectedBoardId.value))
);

const hasActiveFilters = computed(() => {
  const f = boardFilters.value;
  return (f.assignee?.length ?? 0) > 0 || (f.priority?.length ?? 0) > 0 || (f.label?.length ?? 0) > 0;
});

async function fetchTasks() {
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  const bid = selectedBoardId.value;
  if (!wid || !pid || !bid) return;
  boardLoading.value = true;
  try {
    const data = await taskService.list(wid, pid, bid);
    if (Number(selectedBoardId.value) !== Number(bid)) return;
    const raw = data?.columns ?? data?.data?.columns ?? [];
    
    const normalized = Array.isArray(raw)
      ? raw.map((col) => ({
          ...col,
          tasks: Array.isArray(col.tasks) ? col.tasks : (col.tasks?.data ?? []),
        }))
      : [];
    const cols = normalized.length > 0 ? normalized : getColumnsFromBoard();
    columnsWithTasks.value = cols;
    filteredColumnsWithTasks.value = applyFilters(cols);
    displayColumns.value = applySort(filteredColumnsWithTasks.value, sortMode.value);
    boardColumns.value = applySearch(displayColumns.value, searchQuery.value);
    
  } catch {
    if (Number(selectedBoardId.value) !== Number(bid)) return;
    const cols = getColumnsFromBoard();
    columnsWithTasks.value = cols;
    filteredColumnsWithTasks.value = applyFilters(cols);
    displayColumns.value = applySort(filteredColumnsWithTasks.value, sortMode.value);
    boardColumns.value = applySearch(displayColumns.value, searchQuery.value);
  } finally {
    boardLoading.value = false;
  }
}

function getColumnsFromBoard() {
  const bid = selectedBoardId.value;
  const board = boards.value.find((b) => Number(b.id) === Number(bid));
  const cols = board?.columns ?? [];
  return Array.isArray(cols) ? cols.map((c) => ({ ...c, tasks: [] })) : [];
}

function startAddTask(col) {
  addTaskColumn.value = col;
  newTaskTitle.value = '';
  showAddTask.value = true;
}

async function addTask() {
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  const bid = selectedBoardId.value;
  const col = addTaskColumn.value;
  if (!wid || !pid || !bid || !col || !newTaskTitle.value.trim()) return;
  addingTask.value = true;
  try {
    await taskService.create(wid, pid, bid, {
      column_id: col.id,
      title: newTaskTitle.value.trim(),
    });
    showAddTask.value = false;
    addTaskColumn.value = null;
    await fetchTasks();
  } finally {
    addingTask.value = false;
  }
}

const selectedTaskId = ref(null);
const showTaskDrawer = ref(false);
const drawerActivities = ref([]);
const drawerComments = ref([]);
const drawerCommentsLoading = ref(false);
const drawerAttachments = ref([]);
const drawerAttachmentsLoading = ref(false);

function openTask(t) {
  selectedTaskId.value = t.id;
  showTaskDrawer.value = true;
}

async function fetchTaskById(id) {
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  const bid = selectedBoardId.value;
  if (!wid || !pid || !bid) return null;
  return taskService.get(wid, pid, bid, id);
}

async function fetchProjectMembers() {
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  if (!wid || !pid) return;
  try {
    const data = await projectMemberService.list(wid, pid);
    projectMembers.value = Array.isArray(data) ? data : (data?.data ?? []);
  } catch {
    projectMembers.value = [];
  }
}

async function setAssignee(taskId, userId) {
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  const bid = selectedBoardId.value;
  if (!wid || !pid || !bid) return;
  await taskService.setAssignee(wid, pid, bid, taskId, userId);
  await fetchTasks();
  await refreshDrawerActivities();
}

async function updateTaskMeta(taskId, payload) {
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  const bid = selectedBoardId.value;
  if (!wid || !pid || !bid) return;
  await taskService.updateMeta(wid, pid, bid, taskId, payload);
  await fetchTasks();
  await refreshDrawerActivities();
}

async function fetchLabels() {
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  if (!wid || !pid) return;
  try {
    const data = await labelService.list(wid, pid);
    labels.value = Array.isArray(data) ? data : (data?.data ?? []);
  } catch {
    labels.value = [];
  }
}

async function setTaskLabels(taskId, labelIds) {
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  const bid = selectedBoardId.value;
  if (!wid || !pid || !bid) return;
  await taskService.setLabels(wid, pid, bid, taskId, labelIds);
  await fetchTasks();
}

async function addSubtaskToTask(taskId, title) {
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  const bid = selectedBoardId.value;
  if (!wid || !pid || !bid) return;
  const data = await subtaskService.create(wid, pid, bid, taskId, { title });
  return data;
}

async function toggleSubtaskComplete(taskId, subtaskId, isCompleted) {
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  const bid = selectedBoardId.value;
  if (!wid || !pid || !bid) return;
  await subtaskService.update(wid, pid, bid, taskId, subtaskId, { is_completed: isCompleted });
  await fetchTasks();
}

async function fetchProject() {
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  if (!wid || !pid) return;
  try {
    project.value = await projectService.get(wid, pid);
  } catch {
    project.value = null;
  }
}

async function fetchBoards() {
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  if (!wid || !pid) return;
  try {
    const data = await boardService.list(wid, pid);
    boards.value = Array.isArray(data) ? data : (data?.data ?? []);
    if (boards.value.length > 0 && !selectedBoardId.value) {
      const defaultBoard = boards.value.find((b) => b.is_default) ?? boards.value[0];
      selectedBoardId.value = defaultBoard.id;
    }
  } catch {
    boards.value = [];
  }
}

async function createBoard() {
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  if (!wid || !pid || !newBoardName.value.trim()) return;
  creating.value = true;
  try {
    await boardService.create(wid, pid, { name: newBoardName.value.trim() });
    showCreateBoard.value = false;
    newBoardName.value = '';
    await fetchBoards();
  } finally {
    creating.value = false;
  }
}

watch([projectId, () => workspaceStore.activeWorkspaceId], () => {
  fetchProject();
  fetchBoards();
  fetchProjectMembers();
  fetchLabels();
});

watch(selectedBoardId, (id) => {
  if (id) {
    fetchTasks();
  }
}, { immediate: true });

watch(
  [columnsWithTasks, boardFilters],
  () => {
    filteredColumnsWithTasks.value = applyFilters(columnsWithTasks.value);
  },
  { immediate: true, deep: true }
);

watch(
  [filteredColumnsWithTasks, sortMode],
  () => {
    displayColumns.value = applySort(filteredColumnsWithTasks.value, sortMode.value);
  },
  { immediate: true, deep: true }
);

function applySearch(columns, q) {
  if (!columns) return [];
  if (!q || !String(q).trim()) return columns;
  const lower = String(q).trim().toLowerCase();
  return columns.map((col) => ({
    ...col,
    tasks: (col.tasks ?? []).filter((t) => {
      const title = (t.title || '').toLowerCase();
      const desc = (t.description || '').toLowerCase();
      const num = String(t.task_number || '').toLowerCase();
      return title.includes(lower) || desc.includes(lower) || num.includes(lower);
    }),
  }));
}

const setSearchQuery = useDebounceFn((v) => {
  searchQuery.value = v;
}, 300);

watch(searchInput, (v) => setSearchQuery(v));

watch(dragOverColumnId, (id) => {
  document.body.classList.toggle('drag-over-column', id != null);
});

watch(
  [displayColumns, searchQuery],
  () => {
    boardColumns.value = applySearch(displayColumns.value, searchQuery.value);
  },
  { immediate: true, deep: true }
);

async function refreshDrawerActivities() {
  const taskId = selectedTaskId.value;
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  const bid = selectedBoardId.value;
  if (!taskId || !wid || !pid || !bid) return;
  try {
    drawerActivities.value = await taskActivityService.list(wid, pid, bid, taskId) ?? [];
  } catch {
    drawerActivities.value = [];
  }
}

watch([showTaskDrawer, selectedTaskId], async ([open, taskId]) => {
  if (open && taskId) {
    const wid = workspaceStore.activeWorkspaceId;
    const pid = projectId.value;
    const bid = selectedBoardId.value;
    if (wid && pid && bid) {
      await refreshDrawerActivities();
      drawerCommentsLoading.value = true;
      drawerAttachmentsLoading.value = true;
      try {
        const [commentsData, attachmentsData] = await Promise.all([
          commentService.list(wid, pid, bid, taskId),
          attachmentService.list(wid, pid, bid, taskId),
        ]);
        drawerComments.value = Array.isArray(commentsData) ? commentsData : (commentsData?.data ?? []);
        drawerAttachments.value = Array.isArray(attachmentsData) ? attachmentsData : (attachmentsData?.data ?? []);
      } catch {
        drawerComments.value = [];
        drawerAttachments.value = [];
      } finally {
        drawerCommentsLoading.value = false;
        drawerAttachmentsLoading.value = false;
      }
    }
  } else {
    drawerActivities.value = [];
    drawerComments.value = [];
    drawerAttachments.value = [];
  }
});

async function addCommentToTask(body) {
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  const bid = selectedBoardId.value;
  const taskId = selectedTaskId.value;
  if (!wid || !pid || !bid || !taskId) return;
  const comment = await commentService.create(wid, pid, bid, taskId, { body });
  drawerComments.value = [...drawerComments.value, comment];
  await refreshDrawerActivities();
}

async function updateTaskComment(commentId, body) {
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  const bid = selectedBoardId.value;
  const taskId = selectedTaskId.value;
  if (!wid || !pid || !bid || !taskId) return;
  const updated = await commentService.update(wid, pid, bid, taskId, commentId, { body });
  drawerComments.value = drawerComments.value.map((c) => (c.id === commentId ? updated : c));
}

async function deleteTaskComment(commentId) {
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  const bid = selectedBoardId.value;
  const taskId = selectedTaskId.value;
  if (!wid || !pid || !bid || !taskId) return;
  await commentService.delete(wid, pid, bid, taskId, commentId);
  drawerComments.value = drawerComments.value.filter((c) => c.id !== commentId);
}

async function setTaskWatch(taskId) {
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  const bid = selectedBoardId.value;
  if (!wid || !pid || !bid) return;
  await taskService.watch(wid, pid, bid, taskId);
}

async function unsetTaskWatch(taskId) {
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  const bid = selectedBoardId.value;
  if (!wid || !pid || !bid) return;
  await taskService.unwatch(wid, pid, bid, taskId);
}

async function uploadAttachmentToTask(file) {
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  const bid = selectedBoardId.value;
  const taskId = selectedTaskId.value;
  if (!wid || !pid || !bid || !taskId) return;
  const data = await attachmentService.upload(wid, pid, bid, taskId, file);
  drawerAttachments.value = [data, ...drawerAttachments.value];
}

async function deleteTaskAttachment(attachmentId) {
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  const bid = selectedBoardId.value;
  const taskId = selectedTaskId.value;
  if (!wid || !pid || !bid || !taskId) return;
  await attachmentService.delete(wid, pid, bid, taskId, attachmentId);
  drawerAttachments.value = drawerAttachments.value.filter((a) => a.id !== attachmentId);
}

async function downloadTaskAttachment(attachment) {
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  const bid = selectedBoardId.value;
  const taskId = selectedTaskId.value;
  if (!wid || !pid || !bid || !taskId) return;
  const blob = await attachmentService.downloadBlob(wid, pid, bid, taskId, attachment.id);
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = attachment.original_name || 'download';
  a.click();
  URL.revokeObjectURL(url);
}

async function addColumn() {
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  const bid = selectedBoardId.value;
  if (!wid || !pid || !bid || !newColumnName.value.trim()) return;
  addingColumn.value = true;
  try {
    await boardColumnService.create(wid, pid, bid, { name: newColumnName.value.trim() });
    showAddColumn.value = false;
    newColumnName.value = '';
    await fetchTasks();
  } finally {
    addingColumn.value = false;
  }
}

function updateColumnTasks(columnId, newTasks) {
  const col = columnsWithTasks.value.find((c) => c.id === columnId);
  if (!col) return;
  const prevTasks = col.tasks ?? [];
  const prevIds = new Set(prevTasks.map((t) => t?.id).filter(Boolean));
  const addedTask = Array.isArray(newTasks)
    ? newTasks.find((t) => t?.id != null && !prevIds.has(t.id))
    : null;
  const shouldUpdateLocal = !hasActiveFilters.value && !(searchQuery.value || '').trim();
  if (addedTask && newTasks.length > prevTasks.length) {
    const sortOrder = newTasks.findIndex((t) => t?.id === addedTask.id);
    if (sortOrder >= 0) {
      handleTaskMoved({ task: addedTask, columnId: Number(columnId), sortOrder });
    }
  }
  if (shouldUpdateLocal) {
    col.tasks = newTasks;
  }
}

async function handleColumnReorder() {
  reorderingColumns.value = false;
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  const bid = selectedBoardId.value;
  if (!wid || !pid || !bid) return;
  const newOrder = boardColumns.value.map((c) => c.id);
  const prevColumns = JSON.parse(JSON.stringify(columnsWithTasks.value));
  columnsWithTasks.value = newOrder.map((id) =>
    columnsWithTasks.value.find((c) => c.id === id)
  ).filter(Boolean);
  try {
    await boardColumnService.reorder(wid, pid, bid, newOrder);
  } catch {
    columnsWithTasks.value = prevColumns;
    await fetchTasks();
  }
}

async function handleTaskMoved({ task, columnId, sortOrder }) {
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  const bid = selectedBoardId.value;
  if (!wid || !pid || !bid || !task?.id || movingTask.value) return;
  movingTask.value = true;
  const prevColumns = JSON.parse(JSON.stringify(columnsWithTasks.value));
  try {
    await taskService.move(wid, pid, bid, task.id, columnId, sortOrder);
    await fetchTasks();
    if (Number(selectedTaskId.value) === Number(task.id)) {
      await refreshDrawerActivities();
    }
  } catch {
    columnsWithTasks.value = prevColumns;
    filteredColumnsWithTasks.value = applyFilters(columnsWithTasks.value);
    await fetchTasks();
  } finally {
    movingTask.value = false;
  }
}

async function saveColumnName(col, name) {
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  const bid = selectedBoardId.value;
  if (!wid || !pid || !bid || !name?.trim()) return;
  try {
    await boardColumnService.update(wid, pid, bid, col.id, { name: name.trim() });
    const c = columnsWithTasks.value.find((x) => x.id === col.id);
    if (c) c.name = name.trim();
  } catch {
    await fetchTasks();
  }
}

const showDeleteColumnModal = ref(false);
const deleteColumnTarget = ref(null);

function deleteColumn(col) {
  deleteColumnTarget.value = col;
  showDeleteColumnModal.value = true;
}

async function confirmDeleteColumn() {
  const col = deleteColumnTarget.value;
  if (!col) return;
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  const bid = selectedBoardId.value;
  if (!wid || !pid || !bid) return;
  try {
    await boardColumnService.delete(wid, pid, bid, col.id);
    await fetchTasks();
  } catch {
    // Error toast shown by apiClient interceptor
  } finally {
    deleteColumnTarget.value = null;
  }
}

onBeforeUnmount(() => {
  document.body.classList.remove('drag-over-column');
  showTaskDrawer.value = false;
  showCreateBoard.value = false;
  showAddColumn.value = false;
  showAddTask.value = false;
  showDeleteColumnModal.value = false;
});

onMounted(async () => {
  if (workspaceStore.workspaces.length === 0) {
    await workspaceStore.fetchWorkspaces();
  }
  if (!workspaceStore.activeWorkspaceId && workspaceStore.workspaces.length > 0) {
    workspaceStore.setActive(workspaceStore.workspaces[0].id);
  }
  await fetchProject();
  await fetchBoards();
  await fetchTasks();
  await fetchProjectMembers();
  await fetchLabels();
});
</script>
