<template>
  <footer class="border-t border-gray-200 bg-gray-50 py-12">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col items-center justify-between gap-6 md:flex-row">
        <div class="text-center md:text-left">
          <p class="text-lg font-semibold text-gray-900">Kanbix</p>
          <p class="mt-1 text-sm text-gray-600">Project Management Platform</p>
        </div>
        <div class="flex flex-wrap items-center justify-center gap-6">
          <router-link to="/#features" class="text-sm text-gray-600 hover:text-gray-900">Features</router-link>
          <router-link to="/#pricing" class="text-sm text-gray-600 hover:text-gray-900">Pricing</router-link>
          <router-link to="/login" class="text-sm text-gray-600 hover:text-gray-900">Sign In</router-link>
          <a href="#" class="text-sm text-gray-600 hover:text-gray-900">Privacy</a>
          <a href="#" class="text-sm text-gray-600 hover:text-gray-900">Terms</a>
        </div>
      </div>
      <div class="mt-8 border-t border-gray-200 pt-8 text-center">
        <p class="text-sm text-gray-500">
          © 2026 Kanbix · Built with Laravel + Vue
        </p>
      </div>
    </div>
  </footer>
</template>

<script setup>
</script>
