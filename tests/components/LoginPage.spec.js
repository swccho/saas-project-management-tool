import { describe, it, expect, beforeEach } from 'vitest';
import { mount } from '@vue/test-utils';
import { createPinia, setActivePinia } from 'pinia';
import { createRouter, createWebHistory } from 'vue-router';
import LoginPage from '../../resources/js/app/pages/auth/LoginPage.vue';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/login', component: LoginPage },
    { path: '/register', component: { template: '<div>Register</div>' } },
    { path: '/', component: { template: '<div>Home</div>' } },
  ],
});

describe('LoginPage', () => {
  beforeEach(async () => {
    setActivePinia(createPinia());
    await router.push('/login');
  });

  it('renders login form with email and password fields', () => {
    const wrapper = mount(LoginPage, {
      global: {
        plugins: [router],
      },
    });
    expect(wrapper.find('input[type="email"]').exists()).toBe(true);
    expect(wrapper.find('input[type="password"]').exists()).toBe(true);
    expect(wrapper.find('button[type="submit"]').exists()).toBe(true);
  });

  it('displays Sign in heading', () => {
    const wrapper = mount(LoginPage, {
      global: {
        plugins: [router],
      },
    });
    expect(wrapper.text()).toContain('Sign in');
  });
});
