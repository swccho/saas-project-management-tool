<template>
  <section id="how-it-works" class="border-t border-gray-200 bg-white py-20 sm:py-24">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
      <div class="text-center">
        <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
          How it works
        </h2>
        <p class="mt-4 text-lg text-gray-600">
          Get your team up and running in minutes.
        </p>
      </div>
      <div class="mt-16 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
        <div
          v-for="(step, index) in steps"
          :key="step.title"
          class="relative rounded-xl border border-gray-200 bg-white p-6 shadow-sm"
        >
          <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-lg font-semibold text-indigo-600">
            {{ index + 1 }}
          </div>
          <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ step.title }}</h3>
          <p class="mt-2 text-gray-600">{{ step.description }}</p>
          <div v-if="index < steps.length - 1" class="absolute -right-4 top-1/2 hidden -translate-y-1/2 text-gray-300 lg:block">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
const steps = [
  {
    title: 'Create a workspace',
    description: 'Set up your team workspace and invite members. Each workspace keeps projects and tasks organized.',
  },
  {
    title: 'Set up projects and boards',
    description: 'Create projects and add Kanban boards. Customize columns to match your workflow.',
  },
  {
    title: 'Manage tasks and collaborate',
    description: 'Add tasks, assign team members, add labels and due dates. Comment and collaborate in real time.',
  },
  {
    title: 'Track progress with dashboards',
    description: 'Monitor progress with dashboards, analytics, and activity feeds. Stay aligned with your team.',
  },
];
</script>
