import { createRouter, createWebHistory } from 'vue-router';
import CreateMauzo from '@/components/CreateMauzo.vue'; // Assuming CreateMauzo.vue is in the components folder

const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/mauzo/create', component: CreateMauzo, name: 'mauzo.create' },
        // You will add more routes here as you build your SPA
    ],
});

export default router;