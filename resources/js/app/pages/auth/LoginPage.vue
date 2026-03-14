<template>
  <Card class="rounded-xl border border-gray-200 bg-white p-8 shadow-sm">
    <CardHeader>
      <h1 class="text-2xl font-semibold tracking-tight">Sign in to Kanbix</h1>
      <p class="text-sm text-gray-500">Organize projects and collaborate with your team.</p>
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
        <div class="flex items-center justify-between">
          <Checkbox v-model="rememberMe" label="Remember me" />
          <router-link
            to="/forgot-password"
            class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
          >
            Forgot password?
          </router-link>
        </div>
        <p v-if="errors.general" class="text-sm text-red-500">{{ errors.general }}</p>
        <Button type="submit" class="w-full" :loading="loading">
          {{ loading ? 'Signing in...' : 'Sign in' }}
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
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { toast } from 'vue-sonner';
import { useAuthStore } from '../../stores/authStore';
import Button from '../../components/ui/Button.vue';
import Input from '../../components/ui/Input.vue';
import Checkbox from '../../components/ui/Checkbox.vue';
import Card from '../../components/ui/Card.vue';
import CardHeader from '../../components/ui/CardHeader.vue';
import CardContent from '../../components/ui/CardContent.vue';

const REMEMBER_EMAIL_KEY = 'auth_remember_email';

const authStore = useAuthStore();
const router = useRouter();
const route = useRoute();

const email = ref('');
const password = ref('');
const rememberMe = ref(false);
const loading = ref(false);
const errors = reactive({});

onMounted(() => {
  const saved = localStorage.getItem(REMEMBER_EMAIL_KEY);
  if (saved) {
    email.value = saved;
    rememberMe.value = true;
  }
  if (route.query.reset === 'success') {
    toast.success('Your password has been reset. You can now sign in.');
  }
});

async function submit() {
  errors.email = '';
  errors.password = '';
  errors.general = '';
  if (!email.value || !password.value) return;
  loading.value = true;
  try {
    await authStore.login(email.value, password.value);
    if (rememberMe.value) {
      localStorage.setItem(REMEMBER_EMAIL_KEY, email.value);
    } else {
      localStorage.removeItem(REMEMBER_EMAIL_KEY);
    }
    router.push(route.query.redirect || '/app');
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
