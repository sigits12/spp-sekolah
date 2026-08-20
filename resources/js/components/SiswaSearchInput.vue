<template>
  <div class="relative w-full max-w-l">
    <!-- Label Input -->
    <label class="block text-sm font-medium text-slate-700 mb-1">
      Nama Siswa
    </label>

    <!-- Main Input Box Container -->
    <div class="relative">
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Ketik nama..."
        :disabled="isLoading"
        class="w-full px-4 py-2 pr-10 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-all disabled:bg-slate-100 disabled:cursor-not-allowed"
        @focus="selectedSiswa = null"
      />

      <!-- Icon Status: Loading / Clear Button -->
      <div class="absolute right-3 top-2.5 flex items-center">
        <!-- Spinner saat fetching pertama kali -->
        <svg 
          v-if="isLoading" 
          class="animate-spin h-4 w-4 text-blue-500" 
          xmlns="http://www.w3.org/2000/svg" 
          fill="none" 
          viewBox="0 0 24 24"
        >
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>

        <!-- Tombol Clear [X] jika teks diisi -->
        <button
          v-else-if="searchQuery"
          @click="handleClear"
          type="button"
          class="text-slate-400 hover:text-slate-600 transition-colors text-xs bg-slate-100 hover:bg-slate-200 rounded-full w-5 h-5 flex items-center justify-center font-bold"
          title="Bersihkan Pencarian"
        >
          ✕
        </button>
      </div>
    </div>

    <!-- Peringatan Jika API Gagal Load -->
    <p v-if="isError" class="mt-1 text-xs text-red-500">
      Gagal memuat data master siswa. Silakan muat ulang halaman.
    </p>

    <!-- DROPDOWN HASIL PENCARIAN -->
    <!-- Hanya muncul jika user mengetik DAN belum ada siswa yang diklik/dipilih -->
    <div
      v-if="searchQuery.trim() && !selectedSiswa"
      class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-xl max-h-64 overflow-y-auto"
    >
      <!-- State 1: Tidak Ada Hasil Match -->
      <div
        v-if="filteredSiswa.length === 0"
        class="p-4 text-sm text-slate-500 text-center"
      >
        <p class="font-medium">Data tidak ditemukan</p>
        <p class="text-xs text-slate-400 mt-0.5">Coba kata kunci nama atau NIS lain.</p>
      </div>

      <!-- State 2: List Hasil Filter dari Memori (RAM) -->
      <ul v-else class="divide-y divide-slate-100">
        <li
          v-for="siswa in filteredSiswa"
          :key="siswa.id"
          @click="handleSelect(siswa)"
          class="px-4 py-2.5 hover:bg-blue-50 cursor-pointer transition-colors flex justify-between items-center group"
        >
          <div>
            <!-- Nama Siswa -->
            <p class="text-sm font-semibold text-slate-800 group-hover:text-blue-600">
              {{ siswa.nama }}
            </p>
            <!-- NIS -->
            <p class="text-xs text-slate-500">
              KELAS: {{ siswa.kelas || '-' }}
            </p>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

// ==========================================
// 1. EMITS & PROPS (Komunikasi Antar Komponen)
// ==========================================
// Mengirim data siswa terpilih ke komponen Induk (Parent)
const emit = defineEmits(['select-siswa'])

// ==========================================
// 2. STATE REACTIVE (In-Memory Data Store)
// ==========================================
// Menampung SELURUH data dari DB (Hanya terisi 1x)
const siswaMaster = ref([]) 

// Kata kunci yang diketik user di input text
const searchQuery = ref('') 

// Menampung objek siswa yang sedang dipilih oleh user
const selectedSiswa = ref(null) 

// Status loading saat fetch API pertama kali
const isLoading = ref(false) 

// Status error jika fetch ke server gagal
const isError = ref(false)


// ==========================================
// 3. FETCHER DATA (1x Execution ke Laravel)
// ==========================================
const fetchAllSiswa = async () => {
  isLoading.value = true
  isError.value = false

  try {
    // Dipanggil hanya SEKALI saat halaman/menu dibuka
    const response = await fetch('/api/v1/siswa/index', {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })

    if (!response.ok) {
      throw new Error(`HTTP Error: ${response.status}`)
    }

    const result = await response.json()
    
    // Simpan payload array ke variabel reactive di RAM browser
    siswaMaster.value = result.data || []

  } catch (error) {
    console.error('[In-Memory Search] Gagal memuat data master siswa:', error)
    isError.value = true
  } finally {
    isLoading.value = false
  }
}

// ==========================================
// 4. LOGIC SEARCH (Filter di Memori JavaScript)
// ==========================================

// Filter berjalan otomatis secara real-time saat searchQuery diketik
const filteredSiswa = computed(() => {
  // Jika input pencarian kosong/hanya spasi, sembunyikan dropdown (kembalikan array kosong)
  if (!searchQuery.value.trim()) {
    return []
  }

  const query = searchQuery.value.toLowerCase().trim()

  // Saring data siswaMaster yang tersimpan di RAM browser
  return siswaMaster.value
    .filter((siswa) => {
      const matchNama  = siswa.nama?.toLowerCase().includes(query)

      // Kembalikan true jika cocok dengan Nama, NIS, ATAU Kelas
      return matchNama
    })
    .slice(0, 10) // Batasi maksimal 10 hasil agar dropdown tidak memakan ruang layar
})

// ==========================================
// 5. EVENT HANDLERS
// ==========================================

// Panggil saat user mengklik salah satu baris siswa dari dropdown
const handleSelect = (siswa) => {
  selectedSiswa.value = siswa
  
  // Format tampilan teks di dalam input box setelah dipilih
  searchQuery.value = `${siswa.nama} (${siswa.kelas || 'Tanpa Kelas'})`

  // Mengirim data siswa yang dipilih ke Komponen Parent (Induk)
  emit('select-siswa', siswa)
}

// Panggil saat tombol [X] atau reset diklik
const handleClear = () => {
  searchQuery.value = ''
  selectedSiswa.value = null
  emit('select-siswa', null)
}

// Executed saat komponen Vue pertama kali dipasang ke DOM
onMounted(() => {
  fetchAllSiswa()
})
</script>