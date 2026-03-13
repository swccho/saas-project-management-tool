<template>
  <div class="space-y-3">
    <h4 class="text-sm font-medium text-gray-700">Comments</h4>
    <div v-if="loading" class="flex justify-center py-4">
      <div class="h-6 w-6 animate-spin rounded-full border-2 border-indigo-600 border-t-transparent" />
    </div>
    <template v-else>
      <EmptyState
        v-if="comments.length === 0"
        title="No comments yet"
        description="Add one below."
        :compact="true"
        :icon="MessageSquare"
      />
      <div v-else class="space-y-4">
        <div
          v-for="c in comments"
          :key="c.id"
          class="flex gap-3 rounded-lg border border-gray-100 bg-gray-50/50 p-3"
        >
          <Avatar :name="c.user?.name" :status="presenceStatus(c.user?.id)" size="sm" class="shrink-0" />
          <div class="min-w-0 flex-1">
            <div class="flex items-center justify-between gap-2">
              <span class="text-sm font-medium text-gray-900">{{ c.user?.name }}</span>
              <span class="text-xs text-gray-500">{{ formatTime(c.created_at) }}</span>
            </div>
            <div v-if="editingId === c.id" class="mt-2">
              <textarea
                v-model="editBody"
                class="w-full rounded border border-gray-300 px-2 py-1.5 text-sm"
                rows="3"
                @keydown.ctrl.enter="saveEdit(c)"
              />
              <div class="mt-2 flex gap-2">
                <Button size="sm" @click="saveEdit(c)">Save</Button>
                <Button size="sm" variant="secondary" @click="editingId = null; editBody = ''">
                  Cancel
                </Button>
              </div>
            </div>
            <p v-else class="mt-1 whitespace-pre-wrap text-sm text-gray-700" v-html="formatCommentBody(c.body)"></p>
            <div v-if="editingId !== c.id" class="mt-1 flex gap-2">
              <button
                type="button"
                class="text-xs text-indigo-600 hover:underline"
                @click="startEdit(c)"
              >
                Edit
              </button>
              <button
                type="button"
                class="text-xs text-red-600 hover:underline"
                @click="confirmDelete(c)"
              >
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>
      <form class="flex flex-col gap-2" @submit.prevent="submitComment">
        <textarea
          v-model="newCommentBody"
          placeholder="Add a comment..."
          class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
          rows="3"
        />
        <Button type="submit" size="sm" :loading="posting">Comment</Button>
      </form>
    </template>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { MessageSquare } from 'lucide-vue-next';
import Avatar from '../ui/Avatar.vue';
import EmptyState from '../shared/EmptyState.vue';
import Button from '../ui/Button.vue';
import MentionTextarea from './MentionTextarea.vue';
import { formatCommentBody } from '../../utils/formatCommentBody.js';
import { usePresenceStore } from '../../stores/presenceStore';

const presenceStore = usePresenceStore();

function presenceStatus(userId) {
  return userId ? presenceStore.getStatus(userId) : '';
}

const props = defineProps({
  comments: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false },
  members: { type: Array, default: () => [] },
  addComment: { type: Function, default: null },
  updateComment: { type: Function, default: null },
  deleteComment: { type: Function, default: null },
});

const newCommentBody = ref('');
const posting = ref(false);
const editingId = ref(null);
const editBody = ref('');

function formatTime(d) {
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

function startEdit(c) {
  editingId.value = c.id;
  editBody.value = c.body;
}

async function saveEdit(c) {
  if (!props.updateComment || editBody.value.trim() === c.body) {
    editingId.value = null;
    editBody.value = '';
    return;
  }
  try {
    await props.updateComment(c.id, editBody.value.trim());
    editingId.value = null;
    editBody.value = '';
  } catch {
    // Handle error
  }
}

function confirmDelete(c) {
  if (!confirm('Delete this comment?')) return;
  props.deleteComment?.(c.id);
}

async function submitComment() {
  const body = newCommentBody.value?.trim();
  if (!body || !props.addComment) return;
  posting.value = true;
  try {
    await props.addComment(body);
    newCommentBody.value = '';
  } catch {
    // Handle error
  } finally {
    posting.value = false;
  }
}

watch(() => props.comments, () => {
  if (editingId.value && !props.comments.some((c) => c.id === editingId.value)) {
    editingId.value = null;
    editBody.value = '';
  }
});
</script>
