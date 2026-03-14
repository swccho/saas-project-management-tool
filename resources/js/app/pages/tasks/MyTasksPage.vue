<template>
  <div class="space-y-6">
    <h1 class="text-2xl font-semibold tracking-tight">My Tasks</h1>

    <Card v-if="!workspaceStore.activeWorkspaceId">
      <CardContent class="p-0">
        <EmptyState
          title="No workspace selected"
          description="Select a workspace to view your tasks."
          :icon="Building2"
        />
      </CardContent>
    </Card>

    <template v-else>
      <div class="flex flex-wrap gap-2 border-b border-gray-200 pb-4">
        <button
          v-for="tab in VIEW_TABS"
          :key="tab.value"
          :class="[
            'rounded-lg px-3 py-2 text-sm font-medium transition-colors',
            view === tab.value
              ? 'bg-indigo-100 text-indigo-700'
              : 'text-gray-600 hover:bg-gray-100',
          ]"
          @click="view = tab.value"
        >
          {{ tab.label }}
        </button>
      </div>

      <div v-if="loading" class="flex justify-center py-12">
        <div class="h-8 w-8 animate-spin rounded-full border-2 border-indigo-600 border-t-transparent" />
      </div>

      <EmptyState
        v-else-if="tasks.length === 0"
        :title="emptyMessage"
        description="Browse projects to find tasks or get assigned."
      >
        <router-link
          to="/app/projects"
          class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
        >
          Browse projects
        </router-link>
      </EmptyState>

      <div v-else class="space-y-2">
        <div
          v-for="task in tasks"
          :key="task.id"
          class="flex cursor-pointer items-center justify-between gap-4 rounded-lg border border-gray-100 bg-white px-4 py-3 hover:bg-gray-50"
          @click="openTask(task)"
        >
          <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2">
              <span class="text-sm font-medium text-gray-900">{{ task.title }}</span>
              <span class="text-xs text-gray-500">{{ task.project?.key }}-{{ task.task_number }}</span>
            </div>
            <div class="mt-1 flex flex-wrap items-center gap-2">
              <router-link
                :to="`/app/projects/${task.project?.id}`"
                class="text-xs text-indigo-600 hover:underline"
                @click.stop
              >
                {{ task.project?.name }}
              </router-link>
              <span v-if="task.priority" class="rounded px-1.5 py-0.5 text-xs capitalize" :class="priorityClass(task.priority)">
                {{ task.priority }}
              </span>
              <span v-if="task.due_date" class="text-xs" :class="isOverdue(task.due_date) && 'text-red-600'">
                Due {{ formatDate(task.due_date) }}
              </span>
            </div>
          </div>
          <div class="flex shrink-0 items-center gap-2">
            <Avatar v-if="task.assignee" :name="task.assignee.name" size="sm" />
            <ChevronRight class="h-4 w-4 text-gray-400" />
          </div>
        </div>

        <div v-if="meta.last_page > 1" class="flex justify-center gap-2 pt-4">
          <button
            :disabled="meta.current_page <= 1"
            class="rounded border px-3 py-1 text-sm disabled:opacity-50"
            @click="page = Math.max(1, page - 1)"
          >
            Previous
          </button>
          <span class="py-1 text-sm text-gray-500">
            Page {{ meta.current_page }} of {{ meta.last_page }}
          </span>
          <button
            :disabled="meta.current_page >= meta.last_page"
            class="rounded border px-3 py-1 text-sm disabled:opacity-50"
            @click="page = Math.min(meta.last_page, page + 1)"
          >
            Next
          </button>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useWorkspaceStore } from '../../stores/workspaceStore';
import { myTasksService } from '../../services/myTasksService';
import Avatar from '../../components/ui/Avatar.vue';
import Card from '../../components/ui/Card.vue';
import CardContent from '../../components/ui/CardContent.vue';
import EmptyState from '../../components/shared/EmptyState.vue';
import { Building2, ChevronRight } from 'lucide-vue-next';

const VIEW_TABS = [
  { value: 'assigned', label: 'Assigned to me' },
  { value: 'created', label: 'Created by me' },
  { value: 'watching', label: 'Watching' },
  { value: 'overdue', label: 'Overdue' },
  { value: 'due_today', label: 'Due today' },
  { value: 'due_week', label: 'Due this week' },
];

const router = useRouter();
const workspaceStore = useWorkspaceStore();
const view = ref('assigned');
const page = ref(1);
const tasks = ref([]);
const meta = ref({ current_page: 1, last_page: 1, per_page: 20, total: 0 });
const loading = ref(false);

const emptyMessage = computed(() => {
  const labels = {
    assigned: 'No tasks assigned to you.',
    created: 'You have not created any tasks.',
    watching: 'You are not watching any tasks.',
    overdue: 'No overdue tasks.',
    due_today: 'No tasks due today.',
    due_week: 'No tasks due this week.',
  };
  return labels[view.value] ?? 'No tasks found.';
});

function priorityClass(p) {
  const map = {
    urgent: 'bg-red-100 text-red-700',
    high: 'bg-orange-100 text-orange-700',
    medium: 'bg-yellow-100 text-yellow-700',
    low: 'bg-gray-100 text-gray-700',
  };
  return map[p] ?? 'bg-gray-100 text-gray-700';
}

function formatDate(d) {
  if (!d) return '';
  return new Date(d).toLocaleDateString(undefined, {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  });
}

function isOverdue(d) {
  if (!d) return false;
  return new Date(d) < new Date(new Date().toDateString());
}

function openTask(task) {
  if (task.project?.id) {
    router.push({ path: `/app/projects/${task.project.id}/board`, query: { task: task.id } });
  }
}

async function fetchTasks() {
  const wid = workspaceStore.activeWorkspaceId;
  if (!wid) return;
  loading.value = true;
  try {
    const data = await myTasksService.list(wid, { view: view.value, page: page.value, per_page: 20 });
    tasks.value = data?.data ?? [];
    meta.value = data?.meta ?? { current_page: 1, last_page: 1, per_page: 20, total: 0 };
  } catch {
    tasks.value = [];
  } finally {
    loading.value = false;
  }
}

onMounted(() => fetchTasks());
watch([view, page], () => fetchTasks());
watch(() => workspaceStore.activeWorkspaceId, () => {
  page.value = 1;
  fetchTasks();
});
</script>
