<template>
  <div class="min-h-screen bg-gray-100">

    <!-- NAVBAR -->
    <Navbar
      :sidebar-open="sidebarOpen"
      :is-mobile="isMobile"
      @toggle-sidebar="toggleSidebar"
    />

    <!-- MOBILE OVERLAY -->
    <div
      v-if="sidebarOpen && isMobile"
      class="fixed inset-0 bg-black/40 z-40"
      @click="sidebarOpen = false"
    ></div>

    <div class="flex pt-14 relative">

      <!-- SIDEBAR -->
      <Sidebar
        :sidebar-open="sidebarOpen"
        :is-mobile="isMobile"
        @close-sidebar="sidebarOpen = false"
      />

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
import { RouterView } from 'vue-router'

import Navbar from '@/components/Navbar.vue'
import Sidebar from '@/components/Sidebar.vue'

const sidebarOpen = ref(true)
const width = ref(window.innerWidth)

const isMobile = computed(() => width.value < 768)

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}

const handleResize = () => {
  width.value = window.innerWidth

  if (width.value < 768) {
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
</script>
