<template>
  <section id="screenshots" class="border-t border-gray-200 bg-gray-50 py-20 sm:py-24">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
      <div class="text-center">
        <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
          See Kanbix in action
        </h2>
        <p class="mt-4 text-lg text-gray-600">
          A clean, modern interface designed for productivity.
        </p>
      </div>
      <div class="mt-16 grid gap-8 lg:grid-cols-2">
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg">
          <div class="flex h-8 items-center gap-2 border-b border-gray-200 bg-gray-50 px-4">
            <span class="h-2.5 w-2.5 rounded-full bg-red-400" />
            <span class="h-2.5 w-2.5 rounded-full bg-yellow-400" />
            <span class="h-2.5 w-2.5 rounded-full bg-green-400" />
            <span class="ml-2 text-xs text-gray-500">Dashboard</span>
          </div>
          <div class="aspect-video bg-gradient-to-br from-gray-50 to-white p-6">
            <div class="flex h-full gap-4">
              <div class="w-48 shrink-0 space-y-2 rounded-lg border border-gray-200 bg-white p-3">
                <div class="h-3 w-20 rounded bg-gray-200" />
                <div class="space-y-1">
                  <div v-for="i in 5" :key="i" class="h-8 rounded bg-gray-100" />
                </div>
              </div>
              <div class="flex-1 space-y-4 rounded-lg border border-gray-200 bg-white p-4">
                <div class="h-4 w-32 rounded bg-gray-200" />
                <div class="grid grid-cols-3 gap-4">
                  <div v-for="i in 6" :key="i" class="h-20 rounded-lg bg-gray-100" />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg">
          <div class="flex h-8 items-center gap-2 border-b border-gray-200 bg-gray-50 px-4">
            <span class="h-2.5 w-2.5 rounded-full bg-red-400" />
            <span class="h-2.5 w-2.5 rounded-full bg-yellow-400" />
            <span class="h-2.5 w-2.5 rounded-full bg-green-400" />
            <span class="ml-2 text-xs text-gray-500">Kanban Board</span>
          </div>
          <div class="aspect-video bg-gradient-to-br from-gray-50 to-white p-6">
            <div class="flex h-full gap-3">
              <div v-for="col in 4" :key="col" class="flex flex-1 flex-col gap-2 rounded-lg border border-gray-200 bg-gray-50/50 p-3">
                <div class="h-3 w-16 rounded bg-gray-200" />
                <div class="flex-1 space-y-2">
                  <div v-for="i in 3" :key="i" class="h-14 rounded-lg border border-gray-200 bg-white" />
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
