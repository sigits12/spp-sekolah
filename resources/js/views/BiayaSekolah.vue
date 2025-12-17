<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Daftar Biaya Sekolah</h2>
      <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
        </svg>
        Tambah Biaya
      </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="px-6 py-4 text-sm font-semibold text-gray-600">Kategori</th>
            <th class="px-6 py-4 text-sm font-semibold text-gray-600">Nominal</th>
            <th class="px-6 py-4 text-sm font-semibold text-gray-600">Keterangan</th>
            <th class="px-6 py-4 text-sm font-semibold text-gray-600 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr v-for="item in daftarBiaya" :key="item.id" class="hover:bg-gray-50 transition">
            <td class="px-6 py-4">
              <span :class="item.kategori === 'BUKU' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700'" 
                    class="px-3 py-1 rounded-full text-xs font-bold">
                {{ item.kategori }}
              </span>
            </td>
            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
              Rp {{ formatNominal(item.nominal) }}
            </td>
            <td class="px-6 py-4 text-sm text-gray-500">
              {{ item.keterangan }}
            </td>
            <td class="px-6 py-4 text-center">
              <div class="flex justify-center gap-2">
                <button class="text-blue-600 hover:text-blue-800 p-1">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </button>
                <button class="text-red-600 hover:text-red-800 p-1">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const daftarBiaya = ref([])

const fetchBiaya = async () => {
  try {
    // Pastikan URL API sesuai dengan yang Anda buat di Laravel
    const response = await axios.get('/api/v1/keuangan/biaya-sekolah')
    daftarBiaya.value = response.data.data
  } catch (error) {
    console.error("Gagal mengambil data biaya:", error)
  }
}

const formatNominal = (val) => {
  return new Intl.NumberFormat('id-ID').format(val)
}

onMounted(fetchBiaya)
</script>