import '../bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';
import { useAuthStore } from './stores/authStore';

const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(router);

router.isReady().then(async () => {
  const authStore = useAuthStore();
  await authStore.init();
  app.mount('#app');
});
