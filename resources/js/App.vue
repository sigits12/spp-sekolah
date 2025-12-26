<template>
  <div class="flex h-screen bg-gray-100 font-sans">
    
    <aside class="w-64 bg-indigo-900 text-white flex flex-col shadow-lg">
      <div class="p-6 flex items-center gap-3 border-b border-indigo-800">
        <div class="bg-white p-2 rounded-lg">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
          </svg>
        </div>
        <span class="text-xl font-bold tracking-wider">SI-SEKOLAH</span>
      </div>

      <nav class="flex-1 p-4 space-y-2">
        <router-link to="/" class="flex items-center gap-3 p-3 rounded-lg hover:bg-indigo-800 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          <span>Dashboard</span>
        </router-link>

        <router-link to="/biaya-sekolah" class="flex items-center gap-3 p-3 rounded-lg hover:bg-indigo-800 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          <span>Biaya Sekolah</span>
        </router-link>

        <router-link to="/tagihan" class="flex items-center gap-3 p-3 rounded-lg hover:bg-indigo-800 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <span>Tagihan Siswa</span>
        </router-link>

        <router-link to="/pembayaran" class="flex items-center gap-3 p-3 rounded-lg hover:bg-indigo-800 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <span>Pembayaran</span>
        </router-link>
      </nav>

      <div class="p-4 text-xs text-indigo-400 border-t border-indigo-800">
        v1.0.0 &copy; 2025
      </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">
      
      <header class="h-16 bg-white shadow-sm flex items-center justify-end px-8">
        <div class="flex items-center gap-4">
          <div class="text-right">
            <p class="text-sm font-semibold text-gray-700">Admin Keuangan</p>
            <p class="text-xs text-gray-500">Administrator</p>
          </div>
          <div class="h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-700 font-bold border border-indigo-200">
            AD
          </div>
        </div>
      </header>

      <main class="flex-1 overflow-x-hidden overflow-y-auto p-8">
        <router-view></router-view>
      </main>
    </div>

  </div>
</template>

<style>
/* CSS untuk mempercantik link yang sedang aktif */
.router-link-active {
  background-color: #3730a3 !important; /* indigo-800 */
  border-left: 4px solid #fff;
}
</style>