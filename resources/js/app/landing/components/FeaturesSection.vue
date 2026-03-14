<template>
  <section id="features" class="border-t border-gray-200 bg-white py-20 sm:py-24">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
      <div class="text-center">
        <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
          Everything you need to manage projects
        </h2>
        <p class="mt-4 text-lg text-gray-600">
          Kanbix helps teams organize projects, manage tasks, and stay aligned with a clean and modern workflow.
        </p>
      </div>
      <div class="mt-16 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="feature in features"
          :key="feature.title"
          class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition-shadow hover:shadow-md"
        >
          <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
            <component :is="feature.icon" class="h-6 w-6" />
          </div>
          <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ feature.title }}</h3>
          <p class="mt-2 text-gray-600">{{ feature.description }}</p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { FolderKanban, LayoutGrid, ListTodo, Users, Bell, BarChart3 } from 'lucide-vue-next';

const features = [
  {
    title: 'Workspace Management',
    description: 'Create workspaces, organize projects, and keep everything in one place. Invite your team and manage access.',
    icon: FolderKanban,
  },
  {
    title: 'Kanban Boards',
    description: 'Visualize work clearly using modern Kanban boards. Drag and drop tasks through Todo, In Progress, Review, and Done.',
    icon: LayoutGrid,
  },
  {
    title: 'Task Tracking',
    description: 'Track tasks with titles, descriptions, assignees, labels, priorities, due dates, and subtasks.',
    icon: ListTodo,
  },
  {
    title: 'Team Collaboration',
    description: 'Collaborate with your team using comments, mentions, and updates. Stay in sync on every task.',
    icon: Users,
  },
  {
    title: 'Notifications',
    description: 'Get notified when you are mentioned, assigned, or when tasks you care about are updated.',
    icon: Bell,
  },
  {
    title: 'Analytics Dashboard',
    description: 'Track progress with dashboards, analytics, and personal task views. See what matters at a glance.',
    icon: BarChart3,
  },
];
</script>
