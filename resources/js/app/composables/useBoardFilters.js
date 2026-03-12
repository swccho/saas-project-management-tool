import { ref, computed } from 'vue';

/**
 * @typedef {Object} BoardFilters
 * @property {number|null} assignee - User ID or null for all
 * @property {string|null} priority - Priority value or null
 * @property {number|null} label - Label ID or null
 * @property {string|null} status - Status value or null
 * @property {boolean} overdueOnly - Show only overdue tasks
 */

/**
 * @param {BoardFilters} [initial]
 * @returns {{ boardFilters: import('vue').Ref<BoardFilters>, activeFilterCount: import('vue').ComputedRef<number>, clearFilters: () => void, applyFilters: (columns: Array<{ tasks: Array }>) => Array }}
 */
export function useBoardFilters(initial = {}) {
  const boardFilters = ref({
    assignee: initial.assignee ?? null,
    priority: initial.priority ?? null,
    label: initial.label ?? null,
    status: initial.status ?? null,
    overdueOnly: initial.overdueOnly ?? false,
  });

  const activeFilterCount = computed(() => {
    let n = 0;
    if (boardFilters.value.assignee != null) n++;
    if (boardFilters.value.priority != null && boardFilters.value.priority !== '') n++;
    if (boardFilters.value.label != null) n++;
    if (boardFilters.value.status != null && boardFilters.value.status !== '') n++;
    if (boardFilters.value.overdueOnly) n++;
    return n;
  });

  function clearFilters() {
    boardFilters.value = {
      assignee: null,
      priority: null,
      label: null,
      status: null,
      overdueOnly: false,
    };
  }

  /**
   * @param {Array<{ id: number, name: string, tasks: Array }>} columns
   * @returns {Array<{ id: number, name: string, tasks: Array }>}
   */
  function applyFilters(columns) {
    if (!columns || !Array.isArray(columns)) return columns ?? [];
    const f = boardFilters.value;
    const hasAny =
      f.assignee != null ||
      (f.priority != null && f.priority !== '') ||
      f.label != null ||
      (f.status != null && f.status !== '') ||
      f.overdueOnly;
    if (!hasAny) return columns;

    const now = new Date();
    return columns.map((col) => ({
      ...col,
      tasks: (col.tasks ?? []).filter((task) => {
        if (f.assignee != null && task.assigned_to !== f.assignee) return false;
        if (f.priority && task.priority !== f.priority) return false;
        if (f.label != null) {
          const labelIds = (task.labels ?? []).map((l) => l.id);
          if (!labelIds.includes(f.label)) return false;
        }
        if (f.status && task.status !== f.status) return false;
        if (f.overdueOnly) {
          if (!task.due_date) return false;
          const due = new Date(task.due_date);
          if (due >= now || task.status === 'done') return false;
        }
        return true;
      }),
    }));
  }

  return { boardFilters, activeFilterCount, clearFilters, applyFilters };
}
