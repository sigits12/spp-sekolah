<template>
  <div class="overlay">
    <div class="modal">
      <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">

        <div class="bg-white w-full max-w-3xl rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">

            <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold">{{ siswa.nama }}</h2>
            <p class="text-sm text-gray-500">
                Kelas {{ siswa.nama_kelas }} · TP 2025/2026
            </p>
            </div>

            <div v-if="loading" class="animate-pulse space-y-2">
              <div class="h-4 bg-gray-200 rounded"></div>
              <div class="h-4 bg-gray-200 rounded w-2/3"></div>
            </div>
            
            <div v-else-if="data">
              <div class="px-6 py-3 bg-gray-50 border-b grid grid-cols-3 text-center text-sm">
              <div>
                  <p class="text-gray-500">Total</p>
                  <p class="font-semibold">{{ format(data.tagihan) }}</p>
              </div>
              <div>
                  <p class="text-gray-500">Dibayar</p>
                  <p class="font-semibold text-green-600">{{ format(data.dibayar) }}</p>
              </div>
              <div>
                  <p class="text-gray-500">Sisa</p>
                  <p class="font-semibold text-red-600">{{ format(data.sisa) }}</p>
              </div>
              </div>

              <div class="border-b border-gray-200">
              <nav class="flex space-x-2 overflow-x-auto">
                <button 
                  v-for="kategori in categories" 
                  :key="kategori"
                  @click="activeTab = kategori"
                  class="px-4 py-2 text-sm whitespace-nowrap border-b-2"
                  :class="activeTab === kategori
                    ? 'border-blue-500 text-blue-600 font-medium'
                    : 'border-transparent text-gray-500 hover:text-gray-700'
                  "
                >
                  {{ kategori }}
                </button>
              </nav>
              </div>

              <!-- TAB CONTENT -->
              <div class="px-6 py-4 max-h-[50vh] overflow-y-auto">
                <table v-if="isBulanan" class="w-full text-sm border">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="p-2 border">Bulan</th>
                      <th class="p-2 border">Nominal</th>
                      <th class="p-2 border">Dibayar</th>
                      <th class="p-2 border">Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item, i) in currentItems" :key="i">
                      <td class="p-2 border">{{ item.bulan }}</td>
                      <td class="p-2 border">{{ item.nominal }}</td>
                      <td class="p-2 border">{{ item.dibayar }}</td>
                      <td class="p-2 border">
                        <span
                          class="px-2 py-1 rounded text-xs"
                          :class="item.status === 'Lunas'
                            ? 'bg-green-100 text-green-700'
                            : 'bg-red-100 text-red-700'"
                        >
                          {{ item.status }}
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>

                <div v-else class="space-y-2">
                  <div
                    v-for="(item, i) in currentItems"
                    :key="i"
                    class="p-3 border rounded"
                  >
                    <div>Nominal: {{ item.nominal }}</div>
                    <div>Dibayar: {{ item.dibayar }}</div>
                    <div>
                      Status:
                      <span
                        class="px-2 py-1 rounded text-xs"
                        :class="item.status === 'Lunas'
                          ? 'bg-green-100 text-green-700'
                          : 'bg-red-100 text-red-700'"
                      >
                        {{ item.status }}
                      </span>
                    </div>
                  </div>
                </div>

              <!-- SPP TAB -->
              <!-- <table class="w-full text-sm border rounded">
                  <thead class="bg-gray-50">
                  <tr>
                      <th class="px-3 py-2 text-left">Bulan</th>
                      <th class="px-3 py-2 text-right">Nominal</th>
                      <th class="px-3 py-2 text-right">Dibayar</th>
                      <th class="px-3 py-2 text-right">Sisa</th>
                      <th class="px-3 py-2 text-center">Status</th>
                  </tr>
                  </thead>
                  <tbody class="divide-y">
                  <tr>
                      <td class="px-3 py-2">Jan 2025</td>
                      <td class="px-3 py-2 text-right">500.000</td>
                      <td class="px-3 py-2 text-right">500.000</td>
                      <td class="px-3 py-2 text-right">0</td>
                      <td class="px-3 py-2 text-center text-green-600">LUNAS</td>
                  </tr>
                  <tr>
                      <td class="px-3 py-2">Feb 2025</td>
                      <td class="px-3 py-2 text-right">500.000</td>
                      <td class="px-3 py-2 text-right">300.000</td>
                      <td class="px-3 py-2 text-right">200.000</td>
                      <td class="px-3 py-2 text-center text-yellow-600">SEBAGIAN</td>
                  </tr>
                  <tr>
                      <td class="px-3 py-2">Mar 2025</td>
                      <td class="px-3 py-2 text-right">500.000</td>
                      <td class="px-3 py-2 text-right">0</td>
                      <td class="px-3 py-2 text-right">500.000</td>
                      <td class="px-3 py-2 text-center text-red-600">BELUM</td>
                  </tr>
                  </tbody>
              </table> -->

              </div>
            </div>

            <!-- FOOTER -->
            <div class="px-6 py-4 border-t flex justify-end gap-3 bg-gray-50">
            <button class="px-4 py-2 text-sm border rounded" @click="emit('close')">
                Tutup
            </button>
            <button class="px-4 py-2 text-sm bg-blue-600 text-white rounded">
                Bayar
            </button>
            </div>

        </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed, watch } from 'vue'

const props = defineProps({
  loading: Boolean,
  siswa: Object,
  data: Object
})

const activeTab = ref(null)

const categories = computed(() => {
  if (!props.data || !props.data.detail) {
    return []
  }
  return Object.keys(props.data.detail)
})

const format = (value) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0
  }).format(value)
}

const emit = defineEmits(['close'])

const handleEsc = (e) => {
  if (e.key === 'Escape') {
    emit('close')
  }
}

onMounted(() => {
  window.addEventListener('keydown', handleEsc)
})

onUnmounted(() => {
  window.removeEventListener('keydown', handleEsc)
})


watch(
  () => categories.value,
  (val) => {
    if (val.length && !activeTab.value) {
      activeTab.value = val[0]
    }
  },
  { immediate: true }
)

const currentItems = computed(() =>
  props.data.detail?.[activeTab.value] ?? []
)

const isBulanan = computed(() =>
  currentItems.value.length &&
  Object.prototype.hasOwnProperty.call(currentItems.value[0], 'bulan')
)


</script>

<style scoped>
.overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.4);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}
.modal {
  background: white;
  padding: 20px;
  max-width: 500px;
  width: 90%;
  border-radius: 8px;
}
</style>
