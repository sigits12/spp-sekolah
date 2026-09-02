const menus = [
  {
    name: 'Dashboard',
    icon: '📊',
    route: 'dashboard',
    roles: [ 'admin', 'tu', 'wali_kelas', 'kepsek' ]
  },

  {
    name: 'Tagihan',
    icon: '🧾',
    route: 'tagihan',
    roles: ['admin', 'tu']
  },

  {
    name: 'Pembayaran',
    icon: '💰',
    route: 'pembayaran',
    roles: ['admin', 'tu']
  },
  {
    name: 'Riwayat Pembayaran',
    icon: '📋',
    route: 'riwayat-pembayaran',
    roles: ['wali']
  },
  {
    name: 'Pembayaran Orang Tua',
    icon: '💰',
    route: 'pembayaran-orang-tua',
    roles: ['wali']
  },

  // {
  //   name: 'Siswa',
  //   icon: '👨‍🎓',
  //   route: 'siswa'
  // },

  // {
  //   name: 'Laporan',
  //   icon: '📈',
  //   route: 'laporan'
  // },

  // {
  //   name: 'Pengaturan',
  //   icon: '⚙️',
  //   route: 'pengaturan'
  // }
]

export default menus