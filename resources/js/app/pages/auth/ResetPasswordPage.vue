<template>
  <Card class="rounded-xl border border-gray-200 bg-white p-8 shadow-sm">
    <CardHeader>
      <h1 class="text-2xl font-semibold tracking-tight">Create a new password</h1>
      <p class="text-sm text-gray-500">
        Choose a secure password to continue using Kanbix.
      </p>
    </CardHeader>
    <CardContent>
      <form v-if="hasToken" class="space-y-4" @submit.prevent="submit">
        <Input
          v-model="password"
          type="password"
          label="New password"
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
        <Button type="submit" class="w-full" :loading="loading">
          {{ loading ? 'Resetting...' : 'Reset Password' }}
        </Button>
      </form>
      <div v-else class="space-y-4">
        <p class="text-sm text-red-500">
          Invalid or expired reset link. Please request a new password reset.
        </p>
        <router-link
          to="/forgot-password"
          class="inline-flex h-10 w-full items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
        >
          Request new reset link
        </router-link>
      </div>
      <p class="mt-4 text-center text-sm text-gray-500">
        <router-link to="/login" class="font-medium text-indigo-600 hover:text-indigo-500">
          Back to sign in
        </router-link>
      </p>
    </CardContent>
  </Card>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { authService } from '../../services/authService';
import Button from '../../components/ui/Button.vue';
import Input from '../../components/ui/Input.vue';
import Card from '../../components/ui/Card.vue';
import CardHeader from '../../components/ui/CardHeader.vue';
import CardContent from '../../components/ui/CardContent.vue';

const router = useRouter();
const route = useRoute();

const password = ref('');
const passwordConfirmation = ref('');
const loading = ref(false);
const errors = reactive({});
const token = ref('');
const email = ref('');

const hasToken = computed(() => !!(token.value && email.value));

onMounted(() => {
  token.value = route.query.token ?? '';
  email.value = route.query.email ?? '';
});

async function submit() {
  errors.password = '';
  errors.password_confirmation = '';
  errors.general = '';
  if (!password.value || !passwordConfirmation.value) return;
  loading.value = true;
  try {
    await authService.resetPassword({
      email: email.value,
      token: token.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value,
    });
    router.push({ path: '/login', query: { reset: 'success' } });
  } catch (err) {
    const res = err.response?.data;
    if (res?.errors) {
      Object.assign(errors, res.errors);
    } else {
      errors.general = res?.message || 'This reset link is invalid or has expired.';
    }
  } finally {
    loading.value = false;
  }
}
</script>
