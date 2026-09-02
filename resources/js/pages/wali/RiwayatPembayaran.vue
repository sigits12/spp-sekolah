<template>
  <h1 class="text-2xl font-bold mb-4">Riwayat Pembayaran</h1>
  <div class="w-full bg-white rounded-xl border border-slate-200 shadow-sm p-4 md:p-6">
    <h3 class="text-lg font-bold text-slate-800 mb-4">Histori Pembayaran</h3>

    <!-- State Loading -->
    <div v-if="loading" class="py-8 text-center text-sm text-slate-500">
      Memuat riwayat pembayaran...
    </div>

    <!-- State Kosong -->
    <div v-else-if="!historyList || historyList.length === 0" class="py-8 text-center text-sm text-slate-500 border border-dashed border-slate-200 rounded-lg">
      Belum ada riwayat pembayaran untuk siswa ini.
    </div>

    <!-- Tabel Data -->
    <div v-else class="overflow-x-auto rounded-lg border border-slate-200">
      <table class="w-full text-left text-sm text-slate-600 border-collapse">
        <!-- Header Tabel -->
        <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 border-b border-slate-200">
          <tr>
            <th class="px-2 py-1 font-semibold">Tanggal</th>
            <th class="px-2 py-1 font-semibold">Total Bayar</th>
            <th class="px-2 py-1 font-semibold">Metode</th>
            <th 
              v-for="col in columns" 
              :key="col" 
              class="px-2 py-1 font-semibold"
            >
              {{ formatHeaderLabel(col) }}
            </th>
          </tr>
        </thead>

        <!-- Body Tabel -->
        <tbody class="divide-y divide-slate-200 bg-white">
          <tr 
            v-for="item in historyList" 
            :key="item.id" 
            class="hover:bg-slate-50 transition-colors"
          >
            <!-- Tanggal -->
            <td class="px-2 py-1 whitespace-nowrap text-slate-700 font-medium">
              {{ item.tanggal }}
            </td>
            <td class="px-2 py-1 whitespace-nowrap font-bold text-slate-900">
              {{ format(item.total_bayar) }}
            </td>
            <td class="px-2 py-1 whitespace-nowrap">
              <span class="inline-block px-2.5 py-1 text-xs font-medium rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                {{ item.metode || '-' }}
              </span>
            </td>
            <td 
              v-for="col in columns" 
              :key="col" 
              class="px-2 py-1 whitespace-nowrap"
            >
              {{ format(item.kategori?.[col]) }}
            </td>

            <!-- Metode Pembayaran -->
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/api/api'

const auth = useAuthStore()
const historyList = ref([])
const columns = ref([])
const loading = ref(false)

const fetchHistory = async () => {
  try {
    const response = await api.get(`/keuangan/pembayaran/siswa/${auth.user.id}/history`)
    
    historyList.value = response.data.data
    
    columns.value = response.data.meta.columns
  } catch (error) {
    console.log(error.message || 'Terjadi kesalahan sistem.')
    console.log(auth.user)
  } finally {
    loading.value = false
  }
}

onMounted(fetchHistory)

</script>