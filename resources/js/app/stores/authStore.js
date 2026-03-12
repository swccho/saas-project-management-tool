import { defineStore } from 'pinia';
import { authService } from '../services/authService';

const TOKEN_KEY = 'auth_token';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem(TOKEN_KEY),
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
  },

  actions: {
    async init() {
      if (!this.token) return;
      try {
        const data = await authService.fetchMe();
        this.user = data?.user ?? data;
      } catch {
        this.logout();
      }
    },

    setAuth(user, token) {
      this.user = user;
      this.token = token;
      localStorage.setItem(TOKEN_KEY, token);
    },

    logout() {
      this.user = null;
      this.token = null;
      localStorage.removeItem(TOKEN_KEY);
    },

    async login(email, password) {
      const data = await authService.login(email, password);
      this.setAuth(data.user, data.token);
      return data;
    },

    async register(name, email, password, password_confirmation) {
      const data = await authService.register(name, email, password, password_confirmation);
      this.setAuth(data.user, data.token);
      return data;
    },
  },
});
