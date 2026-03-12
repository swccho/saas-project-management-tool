<template>
  <div class="flex min-h-screen items-center justify-center bg-[#FAFAFA] px-4">
    <Card class="w-full max-w-md">
      <CardHeader>
        <h1 class="text-2xl font-semibold tracking-tight">Sign in</h1>
        <p class="text-sm text-gray-500">Enter your credentials to access your account</p>
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
          <Input
            v-model="password"
            type="password"
            label="Password"
            required
            :error="errors.password"
          />
          <p v-if="errors.general" class="text-sm text-red-500">{{ errors.general }}</p>
          <Button type="submit" class="w-full" :loading="loading">
            Sign in
          </Button>
        </form>
        <p class="mt-4 text-center text-sm text-gray-500">
          Don't have an account?
          <router-link to="/register" class="font-medium text-indigo-600 hover:text-indigo-500">
            Sign up
          </router-link>
        </p>
      </CardContent>
    </Card>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../../stores/authStore';
import Button from '../../components/ui/Button.vue';
import Input from '../../components/ui/Input.vue';
import Card from '../../components/ui/Card.vue';
import CardHeader from '../../components/ui/CardHeader.vue';
import CardContent from '../../components/ui/CardContent.vue';

const authStore = useAuthStore();
const router = useRouter();
const route = useRoute();

const email = ref('');
const password = ref('');
const loading = ref(false);
const errors = reactive({});

async function submit() {
  errors.email = '';
  errors.password = '';
  errors.general = '';
  if (!email.value || !password.value) return;
  loading.value = true;
  try {
    await authStore.login(email.value, password.value);
    router.push(route.query.redirect || '/');
  } catch (err) {
    const res = err.response?.data;
    if (res?.errors) {
      Object.assign(errors, res.errors);
    } else {
      errors.general = res?.message || 'Invalid credentials. Please try again.';
    }
  } finally {
    loading.value = false;
  }
}
</script>
