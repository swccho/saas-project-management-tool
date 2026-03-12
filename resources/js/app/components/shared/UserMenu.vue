<template>
  <div ref="menuRef" class="relative">
    <button
      type="button"
      class="flex items-center gap-2 rounded-lg p-1 hover:bg-gray-100"
      @click="open = !open"
    >
      <Avatar :name="authStore.user?.name" size="sm" />
      <ChevronDown class="h-4 w-4" />
    </button>
    <div v-if="open" class="absolute right-0 top-full z-50 mt-1 w-48 rounded-lg border border-gray-200 bg-white py-1 shadow-lg">
      <router-link
        to="/settings"
        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
        @click="open = false"
      >
        Profile
      </router-link>
      <button
        type="button"
        class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50"
        @click="logout"
      >
        Log out
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { ChevronDown } from 'lucide-vue-next';
import { useAuthStore } from '../../stores/authStore';
import Avatar from '../ui/Avatar.vue';

const authStore = useAuthStore();
const router = useRouter();
const open = ref(false);
const menuRef = ref(null);

function logout() {
  authStore.logout();
  open.value = false;
  router.push('/login');
}

function handleClickOutside(e) {
  if (menuRef.value && !menuRef.value.contains(e.target)) open.value = false;
}

onMounted(() => document.addEventListener('click', handleClickOutside));
onUnmounted(() => document.removeEventListener('click', handleClickOutside));
</script>
