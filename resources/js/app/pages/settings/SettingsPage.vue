<template>
  <div class="space-y-6">
    <h1 class="text-2xl font-semibold tracking-tight">Settings</h1>
    <Card>
      <CardHeader>
        <h2 class="text-lg font-semibold">Account</h2>
      </CardHeader>
      <CardContent>
        <p class="text-sm text-gray-600">{{ authStore.user?.name }} ({{ authStore.user?.email }})</p>
      </CardContent>
    </Card>
    <Card>
      <CardHeader>
        <h2 class="text-lg font-semibold">Workspace</h2>
        <p class="text-sm text-gray-600">Manage your active workspace settings.</p>
      </CardHeader>
      <CardContent>
        <router-link
          to="/settings/workspace"
          class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-700"
        >
          Workspace settings →
        </router-link>
      </CardContent>
    </Card>
  </div>
</template>

<script setup>
import { useAuthStore } from '../../stores/authStore';
import Card from '../../components/ui/Card.vue';
import CardHeader from '../../components/ui/CardHeader.vue';
import CardContent from '../../components/ui/CardContent.vue';

const authStore = useAuthStore();
</script>
