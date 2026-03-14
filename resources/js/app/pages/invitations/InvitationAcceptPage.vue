<template>
  <div class="flex min-h-screen items-center justify-center bg-[#FAFAFA] p-4">
    <Card class="w-full max-w-md">
      <CardContent class="pt-6">
        <div v-if="loading" class="py-8 text-center">
          <div class="mx-auto h-8 w-8 animate-spin rounded-full border-2 border-indigo-600 border-t-transparent" />
          <p class="mt-4 text-sm text-gray-500">Loading invitation...</p>
        </div>
        <div v-else-if="!preview" class="py-8 text-center">
          <p class="text-sm text-red-600">Invalid or expired invitation link.</p>
          <router-link to="/" class="mt-4 inline-block text-sm font-medium text-indigo-600">Go to Dashboard</router-link>
        </div>
        <div v-else-if="authStore.isAuthenticated && emailMatches" class="space-y-4">
          <h2 class="text-lg font-semibold">Workspace invitation</h2>
          <p class="text-sm text-gray-600">
            {{ preview.inviter_name }} has invited you to join <strong>{{ preview.workspace_name }}</strong>. Accept to get access.
          </p>
          <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
          <div class="flex gap-2">
            <Button class="flex-1" :loading="accepting" @click="accept">Accept</Button>
            <Button variant="secondary" class="flex-1" :loading="rejecting" @click="reject">Decline</Button>
          </div>
        </div>
        <div v-else-if="authStore.isAuthenticated && !emailMatches" class="space-y-4">
          <h2 class="text-lg font-semibold">Wrong account</h2>
          <p class="text-sm text-gray-600">
            This invitation was sent to <strong>{{ preview.email }}</strong>. Sign out and use that account to accept, or decline.
          </p>
          <div class="flex gap-2">
            <Button variant="secondary" class="flex-1" @click="authStore.logout()">Sign out</Button>
            <Button class="flex-1" :loading="rejecting" @click="reject">Decline</Button>
          </div>
        </div>
        <div v-else class="space-y-4">
          <h2 class="text-lg font-semibold">Workspace invitation</h2>
          <p class="text-sm text-gray-600">
            {{ preview.inviter_name }} has invited you to join <strong>{{ preview.workspace_name }}</strong>. Sign in to accept or create an account.
          </p>
          <div class="flex flex-col gap-2">
            <router-link :to="loginUrl">
              <Button class="w-full">Sign in</Button>
            </router-link>
            <router-link :to="registerUrl">
              <Button variant="secondary" class="w-full">Create account</Button>
            </router-link>
          </div>
        </div>
      </CardContent>
    </Card>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { invitationService } from '../../services/invitationService';
import { useWorkspaceStore } from '../../stores/workspaceStore';
import { useAuthStore } from '../../stores/authStore';
import Card from '../../components/ui/Card.vue';
import CardContent from '../../components/ui/CardContent.vue';
import Button from '../../components/ui/Button.vue';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const workspaceStore = useWorkspaceStore();

const token = computed(() => route.params.token);
const preview = ref(null);
const loading = ref(true);
const error = ref('');
const accepting = ref(false);
const rejecting = ref(false);

const emailMatches = computed(() => {
  if (!preview.value || !authStore.user?.email) return false;
  return preview.value.email?.toLowerCase() === authStore.user.email?.toLowerCase();
});

const loginUrl = computed(() => ({
  path: '/login',
  query: { redirect: `/invitations/accept/${token.value}` },
}));

const registerUrl = computed(() => ({
  path: '/register',
  query: { invitation: token.value },
}));

async function fetchPreview() {
  if (!token.value) return;
  loading.value = true;
  try {
    preview.value = await invitationService.preview(token.value);
  } catch {
    preview.value = null;
  } finally {
    loading.value = false;
  }
}

async function accept() {
  if (!token.value) return;
  accepting.value = true;
  error.value = '';
  try {
    const data = await invitationService.accept(token.value);
    await workspaceStore.fetchWorkspaces();
    workspaceStore.setActive(data.workspace_id);
    router.push('/');
  } catch (e) {
    error.value = e.response?.data?.message ?? 'Failed to accept invitation.';
  } finally {
    accepting.value = false;
  }
}

async function reject() {
  if (!token.value) return;
  rejecting.value = true;
  try {
    await invitationService.reject(token.value);
    router.push('/');
  } catch (e) {
    error.value = e.response?.data?.message ?? 'Failed to decline.';
  } finally {
    rejecting.value = false;
  }
}

onMounted(() => {
  fetchPreview();
});
</script>
