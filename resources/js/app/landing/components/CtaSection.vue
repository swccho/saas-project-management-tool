<template>
  <section class="border-t border-gray-200 bg-indigo-600 py-20 sm:py-24">
    <div class="mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8">
      <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
        Start managing projects with Kanbix.
      </h2>
      <p class="mt-4 text-xl text-indigo-100">
        Bring your team, tasks, and workflows into one clean workspace.
      </p>
      <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
        <router-link
          to="/register"
          class="inline-flex items-center justify-center rounded-lg bg-white px-6 py-3 text-base font-semibold text-indigo-600 shadow-sm transition-colors hover:bg-indigo-50"
        >
          Create Workspace
        </router-link>
        <router-link
          to="/login"
          class="inline-flex items-center justify-center rounded-lg border-2 border-white px-6 py-3 text-base font-semibold text-white transition-colors hover:bg-white/10"
        >
          Sign In
        </router-link>
      </div>
    </div>
  </section>
</template>

<script setup>
</script>
