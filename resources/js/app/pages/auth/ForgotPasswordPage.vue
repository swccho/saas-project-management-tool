<template>
  <Card class="rounded-xl border border-gray-200 bg-white p-8 shadow-sm">
    <CardHeader>
      <h1 class="text-2xl font-semibold tracking-tight">Reset your password</h1>
      <p class="text-sm text-gray-500">
        Enter your email and we will send you a reset link.
      </p>
    </CardHeader>
    <CardContent>
      <form class="space-y-4" @submit.prevent="submit">
        <Input
          v-model="email"
          type="email"
          label="Email"
          placeholder="you@example.com"
          required
          :error="errors.email"
        />
        <p v-if="errors.general" class="text-sm text-red-500">{{ errors.general }}</p>
        <p v-if="success" class="text-sm text-green-600">
          If that email address is in our system, we have sent a password reset link.
        </p>
        <Button type="submit" class="w-full" :loading="loading">
          {{ loading ? 'Sending...' : 'Send Reset Link' }}
        </Button>
      </form>
      <p class="mt-4 text-center text-sm text-gray-500">
        <router-link to="/login" class="font-medium text-indigo-600 hover:text-indigo-500">
          Back to sign in
        </router-link>
      </p>
    </CardContent>
  </Card>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { authService } from '../../services/authService';
import Button from '../../components/ui/Button.vue';
import Input from '../../components/ui/Input.vue';
import Card from '../../components/ui/Card.vue';
import CardHeader from '../../components/ui/CardHeader.vue';
import CardContent from '../../components/ui/CardContent.vue';

const email = ref('');
const loading = ref(false);
const success = ref(false);
const errors = reactive({});

async function submit() {
  errors.email = '';
  errors.general = '';
  success.value = false;
  if (!email.value) return;
  loading.value = true;
  try {
    await authService.requestPasswordReset(email.value);
    success.value = true;
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
