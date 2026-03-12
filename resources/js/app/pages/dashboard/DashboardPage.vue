<template>
  <div class="space-y-6">
    <h1 class="text-2xl font-semibold tracking-tight">Dashboard</h1>
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
      <Card>
        <CardContent class="pt-6">
          <p class="text-sm font-medium text-gray-500">Projects</p>
          <p class="text-2xl font-semibold">0</p>
        </CardContent>
      </Card>
      <Card>
        <CardContent class="pt-6">
          <p class="text-sm font-medium text-gray-500">My Tasks</p>
          <p class="text-2xl font-semibold">0</p>
        </CardContent>
      </Card>
      <Card>
        <CardContent class="pt-6">
          <p class="text-sm font-medium text-gray-500">Workspaces</p>
          <p class="text-2xl font-semibold">{{ workspaceStore.workspaces.length }}</p>
        </CardContent>
      </Card>
    </div>
    <Card>
      <CardHeader>
        <h2 class="text-lg font-semibold">Welcome</h2>
      </CardHeader>
      <CardContent>
        <p class="text-sm text-gray-600">
          Phase 1 foundation is complete. Create a workspace to get started.
        </p>
      </CardContent>
    </Card>
  </div>
</template>

<script setup>
import { useWorkspaceStore } from '../../stores/workspaceStore';
import Card from '../../components/ui/Card.vue';
import CardHeader from '../../components/ui/CardHeader.vue';
import CardContent from '../../components/ui/CardContent.vue';

const workspaceStore = useWorkspaceStore();
</script>
