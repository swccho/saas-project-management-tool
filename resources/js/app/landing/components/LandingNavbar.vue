<template>
  <nav class="fixed top-0 left-0 right-0 z-50 border-b border-gray-200/80 bg-white/80 backdrop-blur-md">
    <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6 lg:px-8">
      <router-link to="/" class="text-xl font-semibold tracking-tight text-gray-900">
        Kanbix
      </router-link>
      <div class="hidden items-center gap-8 md:flex">
        <a href="#features" class="text-sm font-medium text-gray-600 hover:text-gray-900">Features</a>
        <a href="#screenshots" class="text-sm font-medium text-gray-600 hover:text-gray-900">Preview</a>
        <a href="#how-it-works" class="text-sm font-medium text-gray-600 hover:text-gray-900">How it works</a>
        <a href="#pricing" class="text-sm font-medium text-gray-600 hover:text-gray-900">Pricing</a>
        <a href="#benefits" class="text-sm font-medium text-gray-600 hover:text-gray-900">Benefits</a>
      </div>
      <div class="flex items-center gap-3">
        <router-link
          to="/login"
          class="text-sm font-medium text-gray-600 hover:text-gray-900"
        >
          Sign In
        </router-link>
        <router-link
          to="/register"
          class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-indigo-700"
        >
          Get Started
        </router-link>
      </div>
    </div>
  </nav>
</template>

<script setup>
</script>
