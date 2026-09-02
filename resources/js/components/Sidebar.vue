<template>
  <aside
    :class="[
      'bg-white shadow transition-transform duration-300 ease-in-out z-50 w-56',
      'fixed left-0 top-14 h-[calc(100vh-56px)]',

      sidebarOpen
        ? 'translate-x-0'
        : '-translate-x-full'
    ]"
  >

    <ul class="mt-4 space-y-1 px-2">

      <li
        v-for="item in filteredMenus"
        :key="item.route"
      >
        <RouterLink
          :to="{ name: item.route }"

          class="flex items-center gap-3 px-3 py-2 rounded-md
                 transition-colors"

          :class="
            isActive(item.route)
              ? 'bg-blue-50 text-blue-600 font-medium'
              : 'text-gray-700 hover:bg-gray-100'
          "

          @click="handleMenuClick"
        >

          <!-- ICON -->
          <span class="text-lg">
            {{ item.icon }}
          </span>

          <!-- LABEL -->
          <span class="text-sm">
            {{ item.name }}
          </span>

        </RouterLink>
      </li>

    </ul>

  </aside>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRoute, RouterLink } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import menus from '@/services/menus'

const auth = useAuthStore()
const route = useRoute()

const props = defineProps({
  sidebarOpen: {
    type: Boolean,
    default: true
  },

  isMobile: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits([
  'close-sidebar'
])

const isActive = (name) => {
  return route.name === name
}

const handleMenuClick = () => {
  // Di mobile, sidebar ditutup setelah memilih menu
  if (props.isMobile) {
    emit('close-sidebar')
  }
}

const userRole = computed(() => {
  return auth.user?.role ?? null
})

const filteredMenus = computed(() => {
  return menus.filter(menu => {
    if (!menu.roles) {
      return true
    }
    return menu.roles.includes(userRole.value)
  })
})
</script>