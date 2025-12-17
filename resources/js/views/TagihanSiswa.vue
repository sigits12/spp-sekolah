<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <div>
        <h2 class="text-2xl font-bold text-gray-800">Tagihan Siswa</h2>
        <p class="text-sm text-gray-500">Periode: {{ bulanSekarang }} {{ tahunSekarang }}</p>
      </div>
      <div class="flex gap-2">
        <input type="text" v-model="search" placeholder="Cari nama siswa..." class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" />
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Siswa</th>
            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Kategori</th>
            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Bulan</th>
            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Total</th>
            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Status</th>
            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr v-for="tagihan in filteredTagihan" :key="tagihan.id" class="hover:bg-gray-50 transition">
            <td class="px-6 py-4">
              <p class="font-semibold text-gray-800">{{ tagihan.nama_siswa }}</p>
              <p class="text-xs text-gray-400">Tingkat {{ tagihan.tingkat }}</p>
            </td>
            <td class="px-6 py-4">
              <span class="text-sm text-gray-600">{{ tagihan.kategori }}</span>
            </td>
            <td class="px-6 py-4">
              <span class="text-sm text-gray-600">{{ tagihan.bulan }}</span>
            </td>
            <td class="px-6 py-4 font-medium text-gray-900">
              Rp {{ formatNominal(tagihan.nominal) }}
            </td>
            <td class="px-6 py-4">
              <span :class="tagihan.status === 'LUNAS' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" 
                    class="px-3 py-1 rounded-full text-xs font-bold">
                {{ tagihan.status }}
              </span>
            </td>
            <td class="px-6 py-4 text-center">
              <button class="bg-indigo-50 text-indigo-600 px-3 py-1 rounded hover:bg-indigo-600 hover:text-white transition text-sm font-medium">
                Bayar
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const search = ref('')
const bulanSekarang = ref('Desember')
const tahunSekarang = ref('2025')

// Dummy data untuk simulasi (nanti ganti dengan fetch dari API)
const daftarTagihan = ref([
  { id: 1, nama_siswa: 'Budi Santoso', tingkat: 1, kategori: 'SPP', bulan: 'Desember', nominal: 150000, status: 'BELUM BAYAR' },
  { id: 2, nama_siswa: 'Siti Aminah', tingkat: 2, kategori: 'SPP', bulan: 'Desember', nominal: 150000, status: 'LUNAS' },
  { id: 3, nama_siswa: 'Budi Santoso', tingkat: 1, kategori: 'BUKU', bulan: '-', nominal: 500000, status: 'BELUM BAYAR' },
])

const filteredTagihan = computed(() => {
  return daftarTagihan.value.filter(t => 
    t.nama_siswa.toLowerCase().includes(search.value.toLowerCase())
  )
})

const formatNominal = (val) => {
  return new Intl.NumberFormat('id-ID').format(val)
}
</script>