import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/authStore';
import AppLayout from '../layouts/AppLayout.vue';
import AuthLayout from '../layouts/AuthLayout.vue';

const routes = [
  {
    path: '/login',
    component: AuthLayout,
    meta: { guest: true },
    children: [
      { path: '', name: 'login', component: () => import('../pages/auth/LoginPage.vue') },
    ],
  },
  {
    path: '/register',
    component: AuthLayout,
    meta: { guest: true },
    children: [
      { path: '', name: 'register', component: () => import('../pages/auth/RegisterPage.vue') },
    ],
  },
  {
    path: '/forgot-password',
    component: AuthLayout,
    meta: { guest: true },
    children: [
      { path: '', name: 'forgot-password', component: () => import('../pages/auth/ForgotPasswordPage.vue') },
    ],
  },
  {
    path: '/reset-password',
    component: AuthLayout,
    meta: { guest: true },
    children: [
      { path: '', name: 'reset-password', component: () => import('../pages/auth/ResetPasswordPage.vue') },
    ],
  },
  {
    path: '/invitations/accept/:token',
    name: 'invitation-accept',
    component: () => import('../pages/invitations/InvitationAcceptPage.vue'),
  },
  {
    path: '/guide',
    name: 'guide',
    component: () => import('../pages/GuidePage.vue'),
  },
  {
    path: '/',
    component: AppLayout,
    meta: { requiresAuth: true },
    children: [
      { path: '', name: 'dashboard', component: () => import('../pages/dashboard/DashboardPage.vue') },
      { path: 'activity', name: 'workspace-activity', component: () => import('../pages/activities/WorkspaceActivityPage.vue') },
      { path: 'projects', name: 'projects', component: () => import('../pages/projects/ProjectsPage.vue') },
      {
        path: 'projects/:id',
        component: () => import('../layouts/ProjectLayout.vue'),
        children: [
          {
            path: '',
            name: 'project-detail',
            component: () => import('../pages/projects/ProjectDetailPage.vue'),
          },
          {
            path: 'members',
            name: 'project-members',
            component: () => import('../pages/projects/ProjectMembersPage.vue'),
          },
          {
            path: 'board',
            name: 'project-board',
            component: () => import('../pages/projects/ProjectBoardPage.vue'),
          },
          {
            path: 'activity',
            name: 'project-activity',
            component: () => import('../pages/activities/ProjectActivityPage.vue'),
          },
          {
            path: 'settings',
            name: 'project-settings',
            component: () => import('../pages/projects/ProjectSettingsPage.vue'),
          },
        ],
      },
      { path: 'my-tasks', name: 'my-tasks', component: () => import('../pages/tasks/MyTasksPage.vue') },
      { path: 'calendar', name: 'calendar', component: () => import('../pages/calendar/CalendarPage.vue') },
      { path: 'notifications', name: 'notifications', component: () => import('../pages/notifications/NotificationsPage.vue') },
      { path: 'settings', name: 'settings', component: () => import('../pages/settings/SettingsPage.vue') },
      { path: 'settings/profile', name: 'settings-profile', component: () => import('../pages/settings/ProfileSettingsPage.vue') },
      {
        path: 'settings/workspace',
        component: () => import('../layouts/WorkspaceSettingsLayout.vue'),
        children: [
          { path: '', redirect: { name: 'settings-workspace-general' } },
          { path: 'general', name: 'settings-workspace-general', component: () => import('../pages/settings/workspace/WorkspaceGeneralSection.vue') },
          { path: 'members', name: 'settings-workspace-members', component: () => import('../pages/settings/workspace/WorkspaceMembersSection.vue') },
          { path: 'preferences', name: 'settings-workspace-preferences', component: () => import('../pages/settings/workspace/WorkspacePreferencesSection.vue') },
          { path: 'branding', name: 'settings-workspace-branding', component: () => import('../pages/settings/workspace/WorkspaceBrandingSection.vue') },
          { path: 'audit-logs', name: 'settings-workspace-audit-logs', component: () => import('../pages/settings/workspace/WorkspaceAuditLogSection.vue') },
          { path: 'danger', name: 'settings-workspace-danger', component: () => import('../pages/settings/workspace/WorkspaceDangerSection.vue') },
        ],
      },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'login', query: { redirect: to.fullPath } });
  } else if (to.meta.guest && authStore.isAuthenticated) {
    next({ name: 'dashboard' });
  } else {
    next();
  }
});

export default router;
