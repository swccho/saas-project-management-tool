import { ref, computed } from 'vue';

/**
 * @typedef {Object} BoardFilters
 * @property {number[]} assignee - User IDs (empty = all)
 * @property {string[]} priority - Priority values (empty = all)
 * @property {number[]} label - Label IDs (empty = all)
 */

/**
 * @param {BoardFilters} [initial]
 * @returns {{ boardFilters: import('vue').Ref<BoardFilters>, activeFilterCount: import('vue').ComputedRef<number>, clearFilters: () => void, applyFilters: (columns: Array<{ tasks: Array }>) => Array }}
 */
export function useBoardFilters(initial = {}) {
  const boardFilters = ref({
    assignee: Array.isArray(initial.assignee) ? initial.assignee : [],
    priority: Array.isArray(initial.priority) ? initial.priority : [],
    label: Array.isArray(initial.label) ? initial.label : [],
  });

  const activeFilterCount = computed(() => {
    let n = 0;
    if ((boardFilters.value.assignee ?? []).length > 0) n++;
    if ((boardFilters.value.priority ?? []).length > 0) n++;
    if ((boardFilters.value.label ?? []).length > 0) n++;
    return n;
  });

  function clearFilters() {
    boardFilters.value = {
      assignee: [],
      priority: [],
      label: [],
    };
  }

  /**
   * @param {Array<{ id: number, name: string, tasks: Array }>} columns
   * @returns {Array<{ id: number, name: string, tasks: Array }>}
   */
  function applyFilters(columns) {
    if (!columns || !Array.isArray(columns)) return columns ?? [];
    const f = boardFilters.value;
    const assigneeIds = f.assignee ?? [];
    const priorities = f.priority ?? [];
    const labelIds = f.label ?? [];
    const hasAny = assigneeIds.length > 0 || priorities.length > 0 || labelIds.length > 0;
    if (!hasAny) return columns;

    return columns.map((col) => ({
      ...col,
      tasks: (col.tasks ?? []).filter((task) => {
        if (assigneeIds.length > 0 && !assigneeIds.includes(task.assigned_to)) return false;
        if (priorities.length > 0 && !priorities.includes(task.priority)) return false;
        if (labelIds.length > 0) {
          const taskLabelIds = (task.labels ?? []).map((l) => l.id);
          if (!labelIds.some((id) => taskLabelIds.includes(id))) return false;
        }
        return true;
      }),
    }));
  }

  return { boardFilters, activeFilterCount, clearFilters, applyFilters };
}
