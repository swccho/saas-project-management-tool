<template>
  <router-link
    :to="`/projects/${project.id}`"
    class="block rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-shadow hover:shadow-md"
  >
    <div class="flex items-start justify-between gap-2">
      <div class="min-w-0 flex-1">
        <h3 class="truncate font-medium text-gray-900">{{ project.name }}</h3>
        <p class="mt-1 text-xs text-gray-500">{{ project.key }}</p>
        <p v-if="project.description" class="mt-2 line-clamp-2 text-sm text-gray-600">
          {{ project.description }}
        </p>
      </div>
      <span
        v-if="project.color"
        class="h-3 w-3 shrink-0 rounded-full"
        :style="{ backgroundColor: project.color }"
      />
    </div>
    <div v-if="project.status === 'archived'" class="mt-2">
      <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">
        Archived
      </span>
    </div>
  </router-link>
</template>

<script setup>
defineProps({
  project: { type: Object, required: true },
});
</script>
