<template>
  <nav
    class="fixed top-0 left-0 right-0 h-14 bg-white shadow z-50
           flex items-center justify-between px-4"
  >

    <!-- LEFT -->
    <div class="flex items-center gap-3">

      <button
        @click="$emit('toggle-sidebar')"
        class="p-2 rounded hover:bg-gray-100"
        aria-label="Toggle Sidebar"
      >
        ☰
      </button>

      <span class="font-semibold text-lg">
        Admin Bendahara
      </span>

    </div>

    <!-- RIGHT -->
    <div class="flex items-center gap-4">

      <span class="text-sm text-gray-600">
        Bendahara
      </span>

      <button
        @click="handleLogout"
        :disabled="isLoading"
        class="text-sm text-red-500 hover:text-red-700 disabled:opacity-50"
      >
        {{ isLoading ? 'Logging out...' : 'Logout' }}
      </button>

    </div>

  </nav>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

defineProps({
  sidebarOpen: {
    type: Boolean,
    default: true
  },

  isMobile: {
    type: Boolean,
    default: false
  }
})

defineEmits(['toggle-sidebar'])

const auth = useAuthStore()
const router = useRouter()

const isLoading = ref(false)

const handleLogout = async () => {
  isLoading.value = true

  try {
    await auth.logout()
  } finally {
    isLoading.value = false
    router.push('/login')
  }
}
</script>