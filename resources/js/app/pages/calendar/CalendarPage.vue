<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold tracking-tight">Calendar</h1>
      <div class="flex items-center gap-2">
        <button
          type="button"
          class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm hover:bg-gray-50"
          @click="prevMonth"
        >
          ← Prev
        </button>
        <span class="min-w-[140px] text-center font-medium">{{ monthLabel }}</span>
        <button
          type="button"
          class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm hover:bg-gray-50"
          @click="nextMonth"
        >
          Next →
        </button>
      </div>
    </div>

    <Card v-if="!workspaceStore.activeWorkspaceId">
      <CardContent class="p-0">
        <EmptyState
          title="No workspace selected"
          description="Select a workspace to view the calendar."
          :icon="Calendar"
        />
      </CardContent>
    </Card>

    <div v-else-if="loading" class="flex justify-center py-12">
      <div class="h-8 w-8 animate-spin rounded-full border-2 border-indigo-600 border-t-transparent" />
    </div>

    <div v-else class="overflow-hidden rounded-lg border border-gray-200">
      <div class="grid grid-cols-7 border-b border-gray-200 bg-gray-50">
        <div
          v-for="day in WEEKDAYS"
          :key="day"
          class="px-2 py-2 text-center text-xs font-medium text-gray-500"
        >
          {{ day }}
        </div>
      </div>
      <div class="grid grid-cols-7">
        <div
          v-for="(cell, i) in calendarCells"
          :key="i"
          :class="[
            'min-h-[100px] border-b border-r border-gray-100 p-2',
            !cell.isCurrentMonth && 'bg-gray-50/50',
          ]"
        >
          <span
            :class="[
              'text-sm',
              cell.isToday ? 'font-semibold text-indigo-600' : 'text-gray-700',
            ]"
          >
            {{ cell.day }}
          </span>
          <div class="mt-1 space-y-1">
            <button
              v-for="task in getTasksForDate(cell.date)"
              :key="task.id"
              type="button"
              class="block w-full truncate rounded px-2 py-1 text-left text-xs hover:opacity-80"
              :style="{ backgroundColor: (task.project?.color || '#6366F1') + '30', color: task.project?.color || '#6366F1' }"
              @click="openTask(task)"
            >
              {{ task.title }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { Calendar } from 'lucide-vue-next';
import { useWorkspaceStore } from '../../stores/workspaceStore';
import { calendarService } from '../../services/calendarService';
import Card from '../../components/ui/Card.vue';
import CardContent from '../../components/ui/CardContent.vue';
import EmptyState from '../../components/shared/EmptyState.vue';

const WEEKDAYS = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

const router = useRouter();
const workspaceStore = useWorkspaceStore();
const currentDate = ref(new Date());
const tasks = ref([]);
const loading = ref(false);

const monthLabel = computed(() =>
  currentDate.value.toLocaleDateString(undefined, { month: 'long', year: 'numeric' })
);

const rangeStart = computed(() => {
  const d = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth(), 1);
  d.setDate(d.getDate() - d.getDay());
  return d.toISOString().slice(0, 10);
});

const rangeEnd = computed(() => {
  const d = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1, 0);
  d.setDate(d.getDate() + (6 - d.getDay()));
  return d.toISOString().slice(0, 10);
});

const calendarCells = computed(() => {
  const year = currentDate.value.getFullYear();
  const month = currentDate.value.getMonth();
  const first = new Date(year, month, 1);
  const last = new Date(year, month + 1, 0);
  const startPad = first.getDay();
  const daysInMonth = last.getDate();
  const prevMonthDays = new Date(year, month, 0).getDate();

  const cells = [];
  for (let i = 0; i < startPad; i++) {
    const d = prevMonthDays - startPad + i + 1;
    const date = new Date(year, month - 1, d);
    cells.push({
      day: d,
      date: date.toISOString().slice(0, 10),
      isCurrentMonth: false,
      isToday: false,
    });
  }
  const today = new Date().toISOString().slice(0, 10);
  for (let d = 1; d <= daysInMonth; d++) {
    const date = new Date(year, month, d).toISOString().slice(0, 10);
    cells.push({
      day: d,
      date,
      isCurrentMonth: true,
      isToday: date === today,
    });
  }
  const remaining = 42 - cells.length;
  for (let i = 1; i <= remaining; i++) {
    const date = new Date(year, month + 1, i).toISOString().slice(0, 10);
    cells.push({
      day: i,
      date,
      isCurrentMonth: false,
      isToday: date === today,
    });
  }
  return cells;
});

function getTasksForDate(dateStr) {
  return tasks.value.filter((t) => t.due_date === dateStr);
}

function openTask(task) {
  if (task.project?.id) {
    router.push({ path: `/projects/${task.project.id}/board`, query: { task: task.id } });
  }
}

function prevMonth() {
  currentDate.value = new Date(
    currentDate.value.getFullYear(),
    currentDate.value.getMonth() - 1
  );
}

function nextMonth() {
  currentDate.value = new Date(
    currentDate.value.getFullYear(),
    currentDate.value.getMonth() + 1
  );
}

async function fetchTasks() {
  const wid = workspaceStore.activeWorkspaceId;
  if (!wid) return;
  loading.value = true;
  try {
    tasks.value = await calendarService.getTasks(wid, rangeStart.value, rangeEnd.value);
  } catch {
    tasks.value = [];
  } finally {
    loading.value = false;
  }
}

onMounted(() => fetchTasks());
watch([currentDate, () => workspaceStore.activeWorkspaceId], () => fetchTasks());
</script>
