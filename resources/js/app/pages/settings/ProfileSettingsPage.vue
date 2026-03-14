<template>
  <div class="space-y-6">
    <div class="flex items-center gap-4">
      <router-link to="/app/settings" class="text-sm text-gray-600 hover:text-gray-900">
        ← Back to Settings
      </router-link>
    </div>
    <h1 class="text-2xl font-semibold tracking-tight">Profile Settings</h1>

    <Card>
      <CardHeader>
        <h2 class="text-lg font-semibold">Profile Information</h2>
        <p class="text-sm text-gray-600">Update your name and email address.</p>
      </CardHeader>
      <CardContent class="space-y-4">
        <div class="flex items-center gap-6">
          <div class="relative">
            <img
              v-if="avatarPreview || profile?.avatar_url"
              :src="avatarPreview || profile?.avatar_url"
              alt="Avatar"
              class="h-20 w-20 rounded-full object-cover"
            />
            <div
              v-else
              class="flex h-20 w-20 items-center justify-center rounded-full bg-gray-200 text-2xl font-semibold text-gray-600"
            >
              {{ (profile?.name || authStore.user?.name || '?')[0]?.toUpperCase() }}
            </div>
            <label
              class="absolute bottom-0 right-0 flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-indigo-600 text-white shadow hover:bg-indigo-700"
            >
              <Camera class="h-4 w-4" />
              <input
                type="file"
                accept="image/jpeg,image/png,image/jpg,image/gif"
                class="hidden"
                @change="handleAvatarChange"
              />
            </label>
          </div>
          <div class="flex-1">
            <p class="text-sm text-gray-600">Click the camera icon to upload a new avatar.</p>
            <p class="mt-1 text-xs text-gray-500">JPG, PNG or GIF. Max 2MB.</p>
          </div>
        </div>
        <form class="space-y-4" @submit.prevent="submitProfile">
          <Input
            v-model="profileForm.name"
            label="Name"
            required
            :error="profileErrors.name"
          />
          <Input
            v-model="profileForm.email"
            type="email"
            label="Email"
            required
            :error="profileErrors.email"
          />
          <div class="flex gap-2">
            <Button type="submit" :loading="profileSaving">Save changes</Button>
          </div>
        </form>
      </CardContent>
    </Card>

    <Card>
      <CardHeader>
        <h2 class="text-lg font-semibold">Change Password</h2>
        <p class="text-sm text-gray-600">Update your password to keep your account secure.</p>
      </CardHeader>
      <CardContent>
        <form class="space-y-4" @submit.prevent="submitPassword">
          <Input
            v-model="passwordForm.current_password"
            type="password"
            label="Current password"
            required
            :error="passwordErrors.current_password"
          />
          <Input
            v-model="passwordForm.password"
            type="password"
            label="New password"
            required
            :error="passwordErrors.password"
          />
          <Input
            v-model="passwordForm.password_confirmation"
            type="password"
            label="Confirm new password"
            required
            :error="passwordErrors.password_confirmation"
          />
          <div class="flex gap-2">
            <Button type="submit" :loading="passwordSaving">Update password</Button>
          </div>
        </form>
      </CardContent>
    </Card>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { Camera } from 'lucide-vue-next';
import { useAuthStore } from '../../stores/authStore';
import { profileService } from '../../services/profileService';
import Card from '../../components/ui/Card.vue';
import CardHeader from '../../components/ui/CardHeader.vue';
import CardContent from '../../components/ui/CardContent.vue';
import Input from '../../components/ui/Input.vue';
import Button from '../../components/ui/Button.vue';

const authStore = useAuthStore();
const profile = ref(null);
const profileForm = reactive({ name: '', email: '' });
const profileErrors = reactive({});
const profileSaving = ref(false);
const passwordForm = reactive({
  current_password: '',
  password: '',
  password_confirmation: '',
});
const passwordErrors = reactive({});
const passwordSaving = ref(false);
const avatarPreview = ref(null);

onMounted(async () => {
  try {
    profile.value = await profileService.get();
    profileForm.name = profile.value?.name ?? authStore.user?.name ?? '';
    profileForm.email = profile.value?.email ?? authStore.user?.email ?? '';
  } catch {
    profile.value = null;
  }
});

function handleAvatarChange(e) {
  const file = e.target.files?.[0];
  if (!file) return;
  if (file.size > 2 * 1024 * 1024) {
    profileErrors.avatar = 'File must be less than 2MB';
    return;
  }
  profileErrors.avatar = null;
  avatarPreview.value = URL.createObjectURL(file);
  uploadAvatar(file);
  e.target.value = '';
}

async function uploadAvatar(file) {
  try {
    const data = await profileService.uploadAvatar(file);
    if (data) {
      profile.value = data;
      authStore.setAuth(data, authStore.token);
    }
  } catch (err) {
    profileErrors.avatar = err.response?.data?.message || 'Failed to upload avatar';
  }
}

async function submitProfile() {
  profileErrors.name = null;
  profileErrors.email = null;
  profileSaving.value = true;
  try {
    const data = await profileService.update({
      name: profileForm.name,
      email: profileForm.email,
    });
    profile.value = data;
    if (data) {
      authStore.setAuth(data, authStore.token);
    }
  } catch (err) {
    const errors = err.response?.data?.errors ?? {};
    profileErrors.name = errors.name?.[0];
    profileErrors.email = errors.email?.[0];
  } finally {
    profileSaving.value = false;
  }
}

async function submitPassword() {
  passwordErrors.current_password = null;
  passwordErrors.password = null;
  passwordErrors.password_confirmation = null;
  passwordSaving.value = true;
  try {
    await profileService.updatePassword(
      passwordForm.current_password,
      passwordForm.password,
      passwordForm.password_confirmation
    );
    passwordForm.current_password = '';
    passwordForm.password = '';
    passwordForm.password_confirmation = '';
  } catch (err) {
    const errors = err.response?.data?.errors ?? {};
    passwordErrors.current_password = errors.current_password?.[0];
    passwordErrors.password = errors.password?.[0];
    passwordErrors.password_confirmation = errors.password_confirmation?.[0];
  } finally {
    passwordSaving.value = false;
  }
}
</script>
