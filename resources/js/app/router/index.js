import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/authStore';
import AppLayout from '../layouts/AppLayout.vue';

const routes = [
  {
    path: '/login',
    name: 'login',
    component: () => import('../pages/auth/LoginPage.vue'),
    meta: { guest: true },
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('../pages/auth/RegisterPage.vue'),
    meta: { guest: true },
  },
  {
    path: '/',
    component: AppLayout,
    meta: { requiresAuth: true },
    children: [
      { path: '', name: 'dashboard', component: () => import('../pages/dashboard/DashboardPage.vue') },
      { path: 'projects', name: 'projects', component: () => import('../pages/projects/ProjectsPage.vue') },
      {
        path: 'projects/:id',
        name: 'project-detail',
        component: () => import('../pages/projects/ProjectDetailPage.vue'),
      },
      {
        path: 'projects/:id/members',
        name: 'project-members',
        component: () => import('../pages/projects/ProjectMembersPage.vue'),
      },
      {
        path: 'projects/:id/board',
        name: 'project-board',
        component: () => import('../pages/projects/ProjectBoardPage.vue'),
      },
      {
        path: 'projects/:id/settings',
        name: 'project-settings',
        component: () => import('../pages/projects/ProjectSettingsPage.vue'),
      },
      { path: 'my-tasks', name: 'my-tasks', component: () => import('../pages/tasks/MyTasksPage.vue') },
      { path: 'settings', name: 'settings', component: () => import('../pages/settings/SettingsPage.vue') },
      { path: 'settings/workspace', name: 'settings-workspace', component: () => import('../pages/settings/WorkspaceSettingsPage.vue') },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();
  if (!authStore.user && authStore.token) {
    await authStore.init();
  }
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'login', query: { redirect: to.fullPath } });
  } else if (to.meta.guest && authStore.isAuthenticated) {
    next({ name: 'dashboard' });
  } else {
    next();
  }
});

export default router;
