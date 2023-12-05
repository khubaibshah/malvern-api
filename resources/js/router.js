// resources/js/router.js
import { createRouter, createWebHistory } from 'vue-router';
import Home from './components/Home.vue';
import About from './components/About.vue';
import Customers from './components/customers/Customer.vue'

const routes = [
  { path: '/', component: Home },
  { path: '/about', component: About },
  { path: '/customers', component: Customers },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});


router.beforeEach((to, from, next) => {
    // const authStore = useAuthStore();
    // const isAuthenticated = authStore.isAuthed;
    console.log('to', to)
    console.log('from', from)
    if (to.matched.some(record => record.meta.requiresAuth) && !isAuthenticated) {
        next({ name: 'login' });
    } else {
        next();
    }
});


export default router;
