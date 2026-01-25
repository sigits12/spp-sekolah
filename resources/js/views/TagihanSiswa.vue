<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <div>
        <h2 class="text-2xl font-bold text-gray-800">Tagihan Siswa</h2>
        <p class="text-sm text-gray-500">Periode: {{ bulanSekarang }} {{ tahunSekarang }}</p>
      </div>
      <div class="flex gap-2">
        <div v-if="loading" class="text-indigo-600 animate-pulse text-sm font-medium">
          Mencari...
        </div>
        <input type="text" v-model="search" placeholder="Cari nama siswa..." class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" />
      </div>
      <!-- <div class="mb-6 flex justify-between items-center">
        <div class="relative w-full max-w-md">
          <span class="absolute inset-y-0 left-0 flex items-center pl-3">
            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </span>
          <input 
            v-model="search" 
            type="text" 
            placeholder="Cari nama siswa..." 
            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm transition shadow-sm"
          />
        </div>
        
        <div v-if="loading" class="text-indigo-600 animate-pulse text-sm font-medium">
          Mencari...
        </div>
      </div> -->
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Siswa</th>
            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Kategori</th>
            <!-- <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Bulan</th> -->
            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Aksi</th>
          </tr>
        </thead>
        <!-- <pre>{{ JSON.stringify(tagihan, null, 2) }}</pre> -->
        <tbody class="divide-y divide-gray-100">
          <tr v-for="siswa in daftarSiswa" :key="siswa.id" class="hover:bg-gray-50">
          <td class="px-6 py-4">
            <div class="font-bold text-gray-800">{{ siswa.nama }}</div>
            <div class="text-xs text-gray-400 font-medium uppercase tracking-tighter">Kelas {{ siswa.tingkat }}</div>
          </td>
          
          <td class="px-6 py-4">
            <div class="flex flex-wrap gap-2">
              <span v-for="kat in siswa.status_kategori" :key="kat.nama"
                :class="kat.lunas ? 'bg-gray-100 text-gray-400 border-gray-200' : getBadgeColor(kat.nama)"
                class="px-2 py-1 rounded text-[10px] font-extrabold uppercase shadow-sm border flex items-center gap-1"
              >
                <svg v-if="kat.lunas" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                
                {{ kat.nama }}
              </span>
            </div>
          </td>

          <!-- <td class="px-6 py-4 font-mono font-bold text-red-600">
            Rp {{ formatNominal(siswa.total_tunggakan) }}
          </td> -->

          <td class="px-6 py-4 text-right">
            <button @click="openDetail(siswa)" class="text-indigo-600 hover:text-indigo-900 font-semibold text-sm">
              Detail &rsaquo;
            </button>
          </td>
        </tr>
          <!-- <tr v-for="tagihan in daftarTagihan" :key="tagihan.id" class="hover:bg-gray-50 transition">
            <td class="px-6 py-4">


              <p class="font-semibold text-gray-800">{{ tagihan.nama }}</p>
              <p class="text-xs text-gray-400">Kelas {{ tagihan.nama_kelas }}</p>
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
          </tr> -->
        </tbody>
      </table>
      <div class="mt-6 flex items-center justify-between bg-white px-6 py-4 rounded-xl border border-gray-200 shadow-sm">
        <div class="text-sm text-gray-700">
          Menampilkan <span class="font-semibold">{{ pagination.from }}</span> sampai 
          <span class="font-semibold">{{ pagination.to }}</span> dari 
          <span class="font-semibold">{{ pagination.total }}</span> siswa
        </div>
        
        <div class="flex gap-2">
          <button 
            @click="fetchData(pagination.prev_page_url)"
            :disabled="!pagination.prev_page_url"
            :class="!pagination.prev_page_url ? 'opacity-50 cursor-not-allowed' : 'hover:bg-indigo-50 text-indigo-600'"
            class="px-4 py-2 border rounded-lg text-sm font-medium transition"
          >
            Sebelumnya
          </button>

          <button 
            @click="fetchData(pagination.next_page_url)"
            :disabled="!pagination.next_page_url"
            :class="!pagination.next_page_url ? 'opacity-50 cursor-not-allowed' : 'hover:bg-indigo-50 text-indigo-600'"
            class="px-4 py-2 border rounded-lg text-sm font-medium transition"
          >
            Selanjutnya
          </button>
        </div>
      </div>
    </div>
  </div>
  <ModalDetailTagihan
    :show="open"
    :siswa="selectedSiswa"
    @close="open = false"
  />

</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import axios from 'axios'
import ModalDetailTagihan from '@/components/ModalDetailTagihan.vue'

const search = ref('')
const daftarSiswa = ref([])
const bulanSekarang = ref('Desember')
const tahunSekarang = ref('2025')
const loading = ref(false)
const selectedSiswa = ref('')
let searchTimer = null // Variabel untuk menyimpan timer

const open = ref(false)

watch(search, () => {
  // 1. Hapus timer sebelumnya setiap kali user mengetik karakter baru
  clearTimeout(searchTimer)

  // 2. Setel ulang timer baru
  searchTimer = setTimeout(() => {
    fetchData() // Panggil API setelah 500ms berhenti mengetik
  }, 500)
})

const openDetail = async (siswa) => {
  open.value = !open.value
  selectedSiswa.value = siswa
  // loading.value = true
  // try {
  //   await axios.post('/api/v1/keuangan/pembayaran', buildPayload())
  //   alert('Pembayaran berhasil disimpan')
  //   await fetchData()
  // } catch (error) {
  //   console.error("Gagal mengambil data pembayaran:", error)
  // } finally {
  //   loading.value = false
  // }
}

const fetchData = async (url = '/api/v1/keuangan/tagihan-siswa?page=1') => {
  loading.value = true
  try {
    const response = await axios.get(url, {
      params: { 
        search: search.value,
        // Sertakan params lain jika ada, misal: bulan: 12
      }
    })
    daftarSiswa.value = response.data.data
    pagination.value = {
      current_page: response.data.current_page,
      total: response.data.total,
      from: response.data.from,
      to: response.data.to,
      prev_page_url: response.data.prev_page_url,
      next_page_url: response.data.next_page_url
    }
  } catch (error) {
    console.error("Gagal mengambil data tagihan:", error)
  } finally {
    loading.value = false
  }
}

const getBadgeColor = (kategori) => {
  const colors = {
    'SPP': 'bg-blue-100 text-blue-700 border-blue-200',
    'KOMITE': 'bg-purple-100 text-purple-700 border-purple-200',
    'BUKU': 'bg-orange-100 text-orange-700 border-orange-200'
  }
  return colors[kategori] || 'bg-gray-100 text-gray-700'
}

const formatNominal = (val) => new Intl.NumberFormat('id-ID').format(val)

const pagination = ref({
  current_page: 1,
  total: 0,
  from: 0,
  to: 0,
  prev_page_url: null,
  next_page_url: null
})

onMounted(fetchData)

// Dummy data untuk simulasi (nanti ganti dengan fetch dari API)
// const daftarTagihan = ref([
//   { id: 1, nama_siswa: 'Budi Santoso', tingkat: 1, kategori: 'SPP', bulan: 'Desember', nominal: 150000, status: 'BELUM BAYAR' },
//   { id: 2, nama_siswa: 'Siti Aminah', tingkat: 2, kategori: 'SPP', bulan: 'Desember', nominal: 150000, status: 'LUNAS' },
//   { id: 3, nama_siswa: 'Budi Santoso', tingkat: 1, kategori: 'BUKU', bulan: '-', nominal: 500000, status: 'BELUM BAYAR' },
// ])

// const filteredTagihan = computed(() => {
//   return daftarTagihan.value.filter(t => 
//     t.nama_siswa.toLowerCase().includes(search.value.toLowerCase())
//   )
// })

// const formatNominal = (val) => {
//   return new Intl.NumberFormat('id-ID').format(val)
// }
</script>