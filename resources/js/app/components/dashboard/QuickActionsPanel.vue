<template>
  <div class="space-y-3">
    <h3 class="text-sm font-semibold text-gray-900">Quick Actions</h3>
    <div class="flex flex-wrap gap-2">
      <router-link
        v-if="projectId"
        :to="`/projects/${projectId}/board`"
        class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
      >
        <Plus class="h-4 w-4" />
        Add task
      </router-link>
      <router-link
        to="/projects"
        class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
        @click="emitCreateProject"
      >
        <FolderPlus class="h-4 w-4" />
        New project
      </router-link>
      <router-link
        to="/my-tasks"
        class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
      >
        <ListTodo class="h-4 w-4" />
        My tasks
      </router-link>
      <router-link
        to="/calendar"
        class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
      >
        <Calendar class="h-4 w-4" />
        Calendar
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { Plus, FolderPlus, ListTodo, Calendar } from 'lucide-vue-next';

const props = defineProps({
  projectId: { type: [Number, String], default: null },
});

const emit = defineEmits(['create-project']);

function emitCreateProject() {
  emit('create-project');
}
</script>
