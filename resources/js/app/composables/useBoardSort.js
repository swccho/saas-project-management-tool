import { ref, computed } from 'vue';

const SORT_MODES = [
  { value: 'manual', label: 'Manual order' },
  { value: 'newest', label: 'Newest first' },
  { value: 'oldest', label: 'Oldest first' },
  { value: 'due_asc', label: 'Due date (asc)' },
  { value: 'due_desc', label: 'Due date (desc)' },
  { value: 'priority', label: 'Priority' },
  { value: 'alpha', label: 'Title A–Z' },
  { value: 'updated', label: 'Recently updated' },
];

const PRIORITY_ORDER = { urgent: 4, high: 3, medium: 2, low: 1 };

/**
 * @param {Array<{ id: number, name: string, tasks: Array }>} columns
 * @param {string} sortMode
 * @returns {Array<{ id: number, name: string, tasks: Array }>}
 */
export function applySort(columns, sortMode) {
  if (!columns || !Array.isArray(columns) || sortMode === 'manual') {
    return columns ?? [];
  }

  return columns.map((col) => {
    const tasks = [...(col.tasks ?? [])];
    switch (sortMode) {
      case 'newest':
        tasks.sort((a, b) => new Date(b.created_at || 0) - new Date(a.created_at || 0));
        break;
      case 'oldest':
        tasks.sort((a, b) => new Date(a.created_at || 0) - new Date(b.created_at || 0));
        break;
      case 'due_asc':
        tasks.sort((a, b) => {
          const da = a.due_date ? new Date(a.due_date) : new Date(9999, 11, 31);
          const db = b.due_date ? new Date(b.due_date) : new Date(9999, 11, 31);
          return da - db;
        });
        break;
      case 'due_desc':
        tasks.sort((a, b) => {
          const da = a.due_date ? new Date(a.due_date) : new Date(0);
          const db = b.due_date ? new Date(b.due_date) : new Date(0);
          return db - da;
        });
        break;
      case 'priority':
        tasks.sort((a, b) => {
          const pa = PRIORITY_ORDER[a.priority] ?? 0;
          const pb = PRIORITY_ORDER[b.priority] ?? 0;
          return pb - pa;
        });
        break;
      case 'alpha':
        tasks.sort((a, b) => (a.title || '').localeCompare(b.title || ''));
        break;
      case 'updated':
        tasks.sort((a, b) => new Date(b.updated_at || 0) - new Date(a.updated_at || 0));
        break;
      default:
        break;
    }
    return { ...col, tasks };
  });
}

export function useBoardSort() {
  const sortMode = ref('manual');
  return { sortMode, SORT_MODES, applySort };
}
