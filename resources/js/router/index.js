import { createRouter, createWebHistory } from 'vue-router';
import HelloWorld from '../views/HelloWorld.vue';
import Dashboard from '../views/Dashboard.vue';

const routes = [
    {
        path: '/',
        name: 'home',
        component: HelloWorld
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: Dashboard
    },
    { path: '/biaya-sekolah', component: () => import('../views/BiayaSekolah.vue') },
    { path: '/tagihan', component: () => import('../views/TagihanSiswa.vue') },
    { path: '/pembayaran', name: 'pembayaran', component: () => import('../views/PembayaranSiswa.vue') }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;