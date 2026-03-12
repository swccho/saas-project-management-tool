<template>
  <div class="space-y-6">
    <div v-if="project" class="flex items-center justify-between">
      <div class="flex items-center gap-4">
        <router-link :to="`/projects/${projectId}`" class="text-sm text-gray-600 hover:text-gray-900">
          ← Back to Project
        </router-link>
        <div v-if="boards.length > 0" class="flex items-center gap-2">
          <select
            v-model="selectedBoardId"
            class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
          >
            <option v-for="b in boards" :key="b.id" :value="b.id">
              {{ b.name }}
            </option>
          </select>
          <Button size="sm" @click="showCreateBoard = true">New board</Button>
        </div>
      </div>
    </div>

    <template v-if="!project">
      <p class="text-sm text-gray-600">Loading...</p>
    </template>

    <template v-else-if="boards.length === 0">
      <Card>
        <CardContent class="py-12 text-center">
          <h3 class="text-lg font-medium text-gray-900">No boards yet</h3>
          <p class="mt-1 text-sm text-gray-600">Create your first board to get started.</p>
          <Button class="mt-4" @click="showCreateBoard = true">Create board</Button>
        </CardContent>
      </Card>
    </template>

    <template v-else>
      <div class="flex gap-4 overflow-x-auto pb-4">
        <div
          v-for="col in columnsWithTasks"
          :key="col.id"
          class="min-w-[280px] flex-shrink-0 rounded-xl border border-gray-200 bg-gray-50/50 p-4"
        >
          <div class="flex items-center justify-between gap-2">
            <h3
              v-if="!editingColumnId || editingColumnId !== col.id"
              class="text-sm font-semibold text-gray-900"
              @dblclick="startEditColumn(col)"
            >
              {{ col.name }}
            </h3>
            <input
              v-else
              ref="columnNameInput"
              v-model="editingColumnName"
              type="text"
              class="flex-1 rounded border border-indigo-500 px-2 py-1 text-sm"
              @blur="saveColumnName(col)"
              @keydown.enter="saveColumnName(col)"
            />
            <button
              type="button"
              class="rounded p-1 text-gray-400 hover:bg-gray-200 hover:text-gray-600"
              title="Delete column"
              @click="deleteColumn(col)"
            >
              ×
            </button>
          </div>
          <div class="mt-3 space-y-2">
            <TaskCard
              v-for="t in col.tasks"
              :key="t.id"
              :task="t"
              @click="openTask(t)"
            />
            <button
              type="button"
              class="w-full rounded-lg border-2 border-dashed border-gray-300 py-2 text-sm text-gray-500 hover:border-indigo-400 hover:text-indigo-600"
              @click="startAddTask(col)"
            >
              + Add task
            </button>
          </div>
        </div>
        <button
          type="button"
          class="flex min-w-[280px] flex-shrink-0 items-center justify-center rounded-xl border-2 border-dashed border-gray-300 text-sm text-gray-500 hover:border-indigo-400 hover:text-indigo-600"
          @click="showAddColumn = true"
        >
          + Add column
        </button>
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
    />
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
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
import Button from '../../components/ui/Button.vue';
import TaskCard from '../../components/tasks/TaskCard.vue';
import TaskDetailsDrawer from '../../components/tasks/TaskDetailsDrawer.vue';
import Card from '../../components/ui/Card.vue';
import CardContent from '../../components/ui/CardContent.vue';
import Input from '../../components/ui/Input.vue';

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
const editingColumnId = ref(null);
const editingColumnName = ref('');
const columnsWithTasks = ref([]);
const showAddTask = ref(false);
const addTaskColumn = ref(null);
const newTaskTitle = ref('');
const addingTask = ref(false);
const projectMembers = ref([]);
const labels = ref([]);
const taskActivities = ref([]);

const projectId = computed(() => route.params.id);

const currentBoard = computed(() =>
  boards.value.find((b) => b.id === selectedBoardId.value)
);

async function fetchTasks() {
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  const bid = selectedBoardId.value;
  if (!wid || !pid || !bid) return;
  try {
    const data = await taskService.list(wid, pid, bid);
    columnsWithTasks.value = data?.columns ?? [];
  } catch {
    columnsWithTasks.value = [];
  }
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
}

async function updateTaskMeta(taskId, payload) {
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  const bid = selectedBoardId.value;
  if (!wid || !pid || !bid) return;
  await taskService.updateMeta(wid, pid, bid, taskId, payload);
  await fetchTasks();
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
  if (id) fetchTasks();
}, { immediate: true });

watch([showTaskDrawer, selectedTaskId], async ([open, taskId]) => {
  if (open && taskId) {
    const wid = workspaceStore.activeWorkspaceId;
    const pid = projectId.value;
    const bid = selectedBoardId.value;
    if (wid && pid && bid) {
      try {
        drawerActivities.value = await taskActivityService.list(wid, pid, bid, taskId) ?? [];
      } catch {
        drawerActivities.value = [];
      }
    }
  } else {
    drawerActivities.value = [];
  }
});

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
    await fetchBoards();
  } finally {
    addingColumn.value = false;
  }
}

function startEditColumn(col) {
  editingColumnId.value = col.id;
  editingColumnName.value = col.name;
}

async function saveColumnName(col) {
  if (editingColumnId.value !== col.id) return;
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  const bid = selectedBoardId.value;
  if (!wid || !pid || !bid) return;
  try {
    await boardColumnService.update(wid, pid, bid, col.id, { name: editingColumnName.value });
    const board = boards.value.find((b) => b.id === bid);
    if (board?.columns) {
      const c = board.columns.find((x) => x.id === col.id);
      if (c) c.name = editingColumnName.value;
    }
  } finally {
    editingColumnId.value = null;
    editingColumnName.value = '';
  }
}

async function deleteColumn(col) {
  if (!confirm(`Delete column "${col.name}"?`)) return;
  const wid = workspaceStore.activeWorkspaceId;
  const pid = projectId.value;
  const bid = selectedBoardId.value;
  if (!wid || !pid || !bid) return;
  try {
    await boardColumnService.delete(wid, pid, bid, col.id);
    await fetchBoards();
  } catch (e) {
    alert(e.response?.data?.message ?? 'Failed to delete column.');
  }
}

onMounted(async () => {
  if (workspaceStore.workspaces.length === 0) {
    await workspaceStore.fetchWorkspaces();
  }
  await fetchProject();
  await fetchBoards();
  await fetchTasks();
  await fetchProjectMembers();
  await fetchLabels();
});
</script>
