<template>
  <section class="relative overflow-hidden bg-gradient-to-b from-gray-50 to-white pt-24 pb-16 sm:pt-32 sm:pb-24">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
      <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">
        <div class="text-center lg:text-left">
          <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl lg:text-6xl">
            Kanbix
          </h1>
          <p class="mt-4 text-xl font-medium text-gray-700 sm:text-2xl">
            Modern project management for teams.
          </p>
          <p class="mt-3 text-lg text-gray-600">
            Plan work, track progress, and collaborate in one place.
          </p>
          <div class="mt-8 flex flex-wrap items-center justify-center gap-4 lg:justify-start">
            <router-link
              to="/register"
              class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-6 py-3 text-base font-medium text-white shadow-sm transition-colors hover:bg-indigo-700"
            >
              Get Started
            </router-link>
            <router-link
              to="/register"
              class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-3 text-base font-medium text-gray-700 shadow-sm transition-colors hover:bg-gray-50"
            >
              View Demo
            </router-link>
            <router-link
              to="/login"
              class="inline-flex items-center justify-center rounded-lg px-6 py-3 text-base font-medium text-gray-600 hover:text-gray-900"
            >
              Sign In
            </router-link>
          </div>
        </div>
        <div class="relative">
          <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-2xl shadow-gray-200/50">
            <div class="flex h-8 items-center gap-2 border-b border-gray-200 bg-gray-50 px-4">
              <span class="h-3 w-3 rounded-full bg-red-400" />
              <span class="h-3 w-3 rounded-full bg-yellow-400" />
              <span class="h-3 w-3 rounded-full bg-green-400" />
            </div>
            <div class="aspect-video bg-gradient-to-br from-indigo-50 via-white to-gray-50 p-6">
              <div class="flex gap-3">
                <div class="flex flex-1 flex-col gap-2 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                  <div class="h-2 w-3/4 rounded bg-gray-200" />
                  <div class="flex gap-2">
                    <div class="h-12 flex-1 rounded bg-indigo-100" />
                    <div class="h-12 flex-1 rounded bg-indigo-50" />
                    <div class="h-12 flex-1 rounded bg-gray-100" />
                  </div>
                </div>
                <div class="flex flex-1 flex-col gap-2 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                  <div class="h-2 w-2/3 rounded bg-gray-200" />
                  <div class="flex gap-2">
                    <div class="h-12 flex-1 rounded bg-gray-100" />
                    <div class="h-12 flex-1 rounded bg-indigo-50" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
</script>
