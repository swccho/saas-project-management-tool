<template>
  <div class="space-y-6">
    <ProjectMembersSection
      v-if="workspaceStore.activeWorkspaceId && projectId"
      :workspace-id="workspaceStore.activeWorkspaceId"
      :project-id="Number(projectId)"
    />
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useWorkspaceStore } from '../../stores/workspaceStore';
import ProjectMembersSection from '../../components/projects/ProjectMembersSection.vue';

const route = useRoute();
const workspaceStore = useWorkspaceStore();

const projectId = computed(() => route.params.id);

onMounted(async () => {
  if (workspaceStore.workspaces.length === 0) {
    await workspaceStore.fetchWorkspaces();
  }
});
</script>
