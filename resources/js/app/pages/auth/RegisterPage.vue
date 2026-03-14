<template>
  <Card class="rounded-xl border border-gray-200 bg-white p-8 shadow-sm">
    <CardHeader>
      <h1 class="text-2xl font-semibold tracking-tight">Create an account</h1>
      <p v-if="invitationPreview" class="text-sm text-gray-500">
        Join {{ invitationPreview.workspace_name }} – {{ invitationPreview.inviter_name }} invited you
      </p>
      <p v-else class="text-sm text-gray-500">Enter your details to get started</p>
    </CardHeader>
    <CardContent>
      <form class="space-y-4" @submit.prevent="submit">
        <Input
          v-model="name"
          label="Name"
          placeholder="John Doe"
          required
          :error="errors.name"
        />
        <Input
          v-model="email"
          type="email"
          label="Email"
          placeholder="you@example.com"
          required
          :error="errors.email"
          :disabled="!!invitationToken"
        />
        <Input
          v-model="password"
          type="password"
          label="Password"
          required
          :error="errors.password"
        />
        <Input
          v-model="passwordConfirmation"
          type="password"
          label="Confirm password"
          required
          :error="errors.password_confirmation"
        />
        <p v-if="errors.general" class="text-sm text-red-500">{{ errors.general }}</p>
        <p v-if="errors.email" class="text-sm text-gray-600">
          <router-link :to="loginUrl" class="font-medium text-indigo-600">Sign in instead</router-link>
        </p>
        <Button type="submit" class="w-full" :loading="loading">
          {{ loading ? 'Creating account...' : 'Create account' }}
        </Button>
      </form>
      <p class="mt-4 text-center text-sm text-gray-500">
        Already have an account?
        <router-link :to="loginLink" class="font-medium text-indigo-600 hover:text-indigo-500">
          Sign in
        </router-link>
      </p>
    </CardContent>
  </Card>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../../stores/authStore';
import { useWorkspaceStore } from '../../stores/workspaceStore';
import { invitationService } from '../../services/invitationService';
import Button from '../../components/ui/Button.vue';
import Input from '../../components/ui/Input.vue';
import Card from '../../components/ui/Card.vue';
import CardHeader from '../../components/ui/CardHeader.vue';
import CardContent from '../../components/ui/CardContent.vue';

const authStore = useAuthStore();
const router = useRouter();
const route = useRoute();

const name = ref('');
const email = ref('');
const password = ref('');
const passwordConfirmation = ref('');
const loading = ref(false);
const errors = reactive({});
const invitationToken = ref(null);
const invitationPreview = ref(null);

const loginUrl = computed(() => {
  const q = invitationToken.value ? { redirect: `/invitations/accept/${invitationToken.value}` } : {};
  return { path: '/login', query: q };
});

const loginLink = computed(() => {
  const q = invitationToken.value ? { redirect: `/invitations/accept/${invitationToken.value}` } : {};
  return { path: '/login', query: q };
});

async function fetchInvitationPreview() {
  if (!invitationToken.value) return;
  try {
    invitationPreview.value = await invitationService.preview(invitationToken.value);
    if (invitationPreview.value?.email) {
      email.value = invitationPreview.value.email;
    }
  } catch {
    invitationPreview.value = null;
  }
}

onMounted(() => {
  invitationToken.value = route.query.invitation ?? null;
  fetchInvitationPreview();
});

watch(() => route.query.invitation, (val) => {
  invitationToken.value = val ?? null;
  fetchInvitationPreview();
});

async function submit() {
  Object.keys(errors).forEach((k) => delete errors[k]);
  if (!name.value || !email.value || !password.value || !passwordConfirmation.value) return;
  loading.value = true;
  try {
    const data = await authStore.register(
      name.value,
      email.value,
      password.value,
      passwordConfirmation.value,
      invitationToken.value ?? undefined
    );
    if (data.workspace_id) {
      const workspaceStore = useWorkspaceStore();
      await workspaceStore.fetchWorkspaces();
      workspaceStore.setActive(data.workspace_id);
    }
    router.push('/');
  } catch (err) {
    const res = err.response?.data;
    if (res?.errors) {
      Object.assign(errors, res.errors);
    } else {
      errors.general = res?.message || 'Something went wrong. Please try again.';
    }
  } finally {
    loading.value = false;
  }
}
</script>
