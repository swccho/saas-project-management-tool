<template>
  <section id="benefits" class="border-t border-gray-200 bg-white py-20 sm:py-24">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
      <div class="text-center">
        <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
          Why teams choose Kanbix
        </h2>
        <p class="mt-4 text-lg text-gray-600">
          Built for teams who want to work smarter, not harder.
        </p>
      </div>
      <div class="mt-16 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="benefit in benefits"
          :key="benefit.title"
          class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm"
        >
          <h3 class="text-lg font-semibold text-gray-900">{{ benefit.title }}</h3>
          <p class="mt-2 text-gray-600">{{ benefit.description }}</p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
const benefits = [
  {
    title: 'Organize team work in one place',
    description: 'Bring projects, tasks, and conversations together. No more switching between tools or losing context.',
  },
  {
    title: 'Reduce project chaos',
    description: 'Clear workflows, visible progress, and structured boards keep everyone aligned and focused.',
  },
  {
    title: 'Improve collaboration',
    description: 'Comments, mentions, and activity feeds make it easy to communicate and stay in sync.',
  },
  {
    title: 'Track progress faster',
    description: 'Dashboards and personal task views give you instant visibility into what matters.',
  },
  {
    title: 'Keep tasks, deadlines, and updates aligned',
    description: 'Due dates, priorities, and notifications ensure nothing falls through the cracks.',
  },
  {
    title: 'Scale with your team',
    description: 'Workspaces, roles, and permissions grow with you. Add members and projects as you need.',
  },
];
</script>
