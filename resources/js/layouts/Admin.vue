<template>
  <div class="min-h-screen bg-gray-100">
    <!-- NAVBAR -->
    <nav class="fixed top-0 left-0 right-0 h-14 bg-white shadow z-50 flex items-center justify-between px-4">
      <div class="flex items-center gap-3">
        <button
          @click="toggleSidebar"
          class="p-2 rounded hover:bg-gray-100"
        >
          ☰
        </button>
        <span class="font-semibold text-lg">Admin Bendahara</span>
      </div>

      <div class="flex items-center gap-4">
        <span class="text-sm text-gray-600">Bendahara</span>
        <button class="text-sm text-red-500">Logout</button>
      </div>
    </nav>

    <!-- OVERLAY (MOBILE ONLY) -->
    <div
      v-if="sidebarOpen && isMobile"
      class="fixed inset-0 bg-black/40 z-40"
      @click="sidebarOpen = false"
    ></div>

    <div class="flex pt-14 relative">
      <!-- SIDEBAR -->
<aside
  :class="[
    'bg-white shadow transition-transform duration-300 ease-in-out z-50 w-56',
    'fixed left-0 top-14 h-[calc(100vh-56px)]',
    sidebarOpen ? 'translate-x-0' : '-translate-x-full'
  ]"
>

        <ul class="mt-4 space-y-1">
          <li v-for="item in menus" :key="item.route">
  <RouterLink
    :to="{ name: item.route }"
    class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors"
    :class="isActive(item.route)
      ? 'bg-blue-50 text-blue-600 font-medium'
      : 'text-gray-700 hover:bg-gray-100'"
    @click="isMobile && (sidebarOpen = false)"
  >
    <span class="text-lg">{{ item.icon }}</span>

    <span v-if="!isMobile || sidebarOpen" class="text-sm">
      {{ item.name }}
    </span>
  </RouterLink>
</li>


        </ul>
      </aside>

      <!-- MAIN CONTENT -->
<main
  :class="[
    'flex-1 p-2 transition-all duration-300',
    !isMobile && sidebarOpen ? 'ml-56' : 'ml-0'
  ]"
>
  <RouterView />
</main>

    </div>
  </div>
</template>


<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

import { useRoute, RouterView } from 'vue-router'

const route = useRoute()

const isActive = (name) => route.name === name

const showLabel = computed(() => !isMobile.value || sidebarOpen.value)

const sidebarOpen = ref(true)
const width = ref(window.innerWidth)

const isMobile = computed(() => width.value < 768)

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}

const handleResize = () => {
  width.value = window.innerWidth

  // AUTO BEHAVIOR
  if (isMobile.value) {
    sidebarOpen.value = false
  } else {
    sidebarOpen.value = true
  }
}

onMounted(() => {
  handleResize()
  window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
})

const menus = [
  { name: 'Dashboard', icon: '📊', route: 'dashboard' },
  // { name: 'Siswa', icon: '👨‍🎓', route: 'siswa' },
  { name: 'Tagihan', icon: '🧾', route: 'tagihan' },
  { name: 'Pembayaran', icon: '💰', route: 'pembayaran' },
  // { name: 'Laporan', icon: '📈', route: 'laporan' },
  // { name: 'Pengaturan', icon: '⚙️', route: 'pengaturan' },
]

</script>