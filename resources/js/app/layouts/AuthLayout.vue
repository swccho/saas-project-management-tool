<template>
  <div class="flex min-h-screen flex-col lg:flex-row">
    <!-- Left panel: Branding (hidden on mobile, stacked on tablet, split on desktop) -->
    <div
      class="hidden flex-col justify-center px-8 py-12 md:flex md:min-h-[40vh] lg:min-h-full lg:w-1/2 lg:px-12"
      :class="[
        'bg-gradient-to-br from-indigo-50 via-white to-gray-50',
      ]"
    >
      <div class="mx-auto max-w-md">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">
          SaaS PM
        </h1>
        <p class="mt-3 text-lg font-medium text-gray-700">
          Manage projects, tasks, and teams effortlessly.
        </p>
        <p class="mt-2 text-sm text-gray-500">
          Plan your work, track progress, and collaborate with your team in one place.
        </p>
        <!-- Minimal kanban illustration -->
        <div class="mt-12 flex gap-3">
          <div class="flex flex-1 flex-col gap-2 rounded-lg border border-gray-200 bg-white/80 p-3 shadow-sm">
            <div class="h-2 max-w-[75%] rounded bg-gray-200" />
            <div class="h-8 rounded bg-indigo-100" />
            <div class="h-8 rounded bg-indigo-50" />
            <div class="h-8 rounded bg-gray-100" />
          </div>
          <div class="flex flex-1 flex-col gap-2 rounded-lg border border-gray-200 bg-white/80 p-3 shadow-sm">
            <div class="h-2 max-w-[66%] rounded bg-gray-200" />
            <div class="h-8 rounded bg-gray-100" />
            <div class="h-8 rounded bg-indigo-50" />
          </div>
          <div class="flex flex-1 flex-col gap-2 rounded-lg border border-gray-200 bg-white/80 p-3 shadow-sm">
            <div class="h-2 max-w-[80%] rounded bg-gray-200" />
            <div class="h-8 rounded bg-gray-100" />
          </div>
        </div>
      </div>
    </div>

    <!-- Right panel: Form -->
    <div class="flex w-full flex-1 flex-col items-center justify-center bg-[#FAFAFA] px-4 py-12 lg:w-1/2">
      <div class="w-full max-w-md">
        <router-view />
      </div>
    </div>
  </div>
</template>

<script setup>
</script>
