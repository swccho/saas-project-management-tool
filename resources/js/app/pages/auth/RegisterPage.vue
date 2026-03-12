<template>
  <div class="flex min-h-screen items-center justify-center bg-[#FAFAFA] px-4">
    <Card class="w-full max-w-md">
      <CardHeader>
        <h1 class="text-2xl font-semibold tracking-tight">Create an account</h1>
        <p class="text-sm text-gray-500">Enter your details to get started</p>
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
          <Button type="submit" class="w-full" :loading="loading">
            Create account
          </Button>
        </form>
        <p class="mt-4 text-center text-sm text-gray-500">
          Already have an account?
          <router-link to="/login" class="font-medium text-indigo-600 hover:text-indigo-500">
            Sign in
          </router-link>
        </p>
      </CardContent>
    </Card>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/authStore';
import Button from '../../components/ui/Button.vue';
import Input from '../../components/ui/Input.vue';
import Card from '../../components/ui/Card.vue';
import CardHeader from '../../components/ui/CardHeader.vue';
import CardContent from '../../components/ui/CardContent.vue';

const authStore = useAuthStore();
const router = useRouter();

const name = ref('');
const email = ref('');
const password = ref('');
const passwordConfirmation = ref('');
const loading = ref(false);
const errors = reactive({});

async function submit() {
  Object.keys(errors).forEach((k) => delete errors[k]);
  if (!name.value || !email.value || !password.value || !passwordConfirmation.value) return;
  loading.value = true;
  try {
    await authStore.register(name.value, email.value, password.value, passwordConfirmation.value);
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
