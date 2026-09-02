import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import Admin from '../layouts/Admin.vue'
import Wali from '../layouts/Wali.vue'
import Dashboard from '../pages/Dashboard.vue'
// import Siswa from '@/pages/Siswa.vue'
import Tagihan from '../pages/TagihanSiswa.vue'
import Pembayaran from '../pages/PembayaranSiswa.vue'
import PembayaranOrangTua from '../pages/PembayaranOrangTua.vue'
import Login from '../pages/Login.vue'
import RiwayatPembayaran from '../pages/wali/RiwayatPembayaran.vue'
import DashboardLayout from '@/layouts/DashboardLayout.vue'
// import Laporan from '@/pages/Laporan.vue'
// import Pengaturan from '@/pages/Pengaturan.vue'

const routes = [
  {
    path: '/login',
    name: 'login',
    meta: { guest: true },
    component: Login,
  },
  {
    path: '/',
    component: DashboardLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: 'dashboard',
        name: 'dashboard',
        component: Dashboard,
        meta: { requiresAuth: true, roles: [ 'admin', 'tu', 'wali_kelas', 'kepsek', 'wali' ] }
      },
      {
        path: 'pembayaran',
        name: 'pembayaran',
        component: Pembayaran,
        meta: { requiresAuth: true, roles: [ 'admin', 'tu', 'wali_kelas', 'kepsek' ] }
      },
      {
        path: 'tagihan',
        name: 'tagihan',
        component: Tagihan,
        meta: { requiresAuth: true, roles: [ 'admin', 'tu', 'wali_kelas', 'kepsek' ] }
      },
      {
        path: 'riwayat-pembayaran',
        name: 'riwayat-pembayaran',
        component: RiwayatPembayaran,
        meta: { requiresAuth: true, roles: [ 'wali' ] }
      },
      {
        path: 'pembayaran-orang-tua',
        name: 'pembayaran-orang-tua',
        component: PembayaranOrangTua,
        meta: { requiresAuth: true, roles: [ 'wali' ] }
      },
    ],
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to) => {
  const auth = useAuthStore()

  // Belum login
  if (
    to.meta.requiresAuth &&
    !auth.isAuthenticated
  ) {
    return {
      name: 'login'
    }
  }

  // Sudah login tetapi mencoba ke login
  if (
    to.meta.guest &&
    auth.isAuthenticated
  ) {
    return {
      name: 'dashboard'
    }
  }

  // Cek role
  if (to.meta.roles) {
    const userRole = auth.user?.role

    if (!to.meta.roles.includes(userRole)) {
      return {
        name: 'dashboard'
      }
    }
  }

  return true
})

export default router