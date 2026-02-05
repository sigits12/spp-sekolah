// import { createRouter, createWebHistory } from 'vue-router';
// import HelloWorld from '../views/HelloWorld.vue';
// import Dashboard from '../views/Dashboard.vue';

// const routes = [
//     {
//         path: '/',
//         redirect: { name: 'dashboard' }
//     },
//     {
//         path: '/dashboard',
//         name: 'dashboard',
//         component: Dashboard
//     },
//     { path: '/biaya-sekolah', component: () => import('../views/BiayaSekolah.vue') },
//     { path: '/tagihan', component: () => import('../views/TagihanSiswa.vue') },
//     { path: '/pembayaran', name: 'pembayaran', component: () => import('../views/PembayaranSiswa.vue') }
// ];

// const router = createRouter({
//     history: createWebHistory(),
//     routes
// });

// export default router;

import { createRouter, createWebHistory } from 'vue-router'
import Admin from '../layouts/Admin.vue'
import Dashboard from '../pages/Dashboard.vue'
// import Siswa from '@/pages/Siswa.vue'
import Tagihan from '../pages/TagihanSiswa.vue'
import Pembayaran from '../pages/PembayaranSiswa.vue'
import Login from '../pages/Login.vue'
// import Laporan from '@/pages/Laporan.vue'
// import Pengaturan from '@/pages/Pengaturan.vue'

// const routes = [
//   { path: '/', name: 'dashboard', component: Dashboard },
// //   { path: '/siswa', name: 'siswa', component: Siswa },
// //   { path: '/tagihan', name: 'tagihan', component: Tagihan },
//   { path: '/pembayaran', name: 'pembayaran', component: Pembayaran },
// //   { path: '/laporan', name: 'laporan', component: Laporan },
// //   { path: '/pengaturan', name: 'pengaturan', component: Pengaturan },
// ]

const routes = [
  {
    path: '/',
    component: Admin,
    children: [
      {
        path: 'login',
        name: 'login',
        component: Login,
      },
      {
        path: '',
        name: 'dashboard',
        component: Dashboard,
      },
      {
        path: 'pembayaran',
        name: 'pembayaran',
        component: Pembayaran,
      },
      {
        path: 'tagihan',
        name: 'tagihan',
        component: Tagihan,
      },
    ],
  },
]

export default createRouter({
  history: createWebHistory(),
  routes,
})
