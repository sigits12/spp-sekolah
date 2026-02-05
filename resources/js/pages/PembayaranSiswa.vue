<template>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
      <div class="flex items-center justify-between mb-2">
        <!-- LEFT: TITLE -->
        <h1 class="text-xl font-semibold text-gray-800">
          Pembayaran
        </h1>

        <!-- RIGHT: PERIODE -->
        <div class="text-sm text-gray-600 bg-white px-3 py-1.5 rounded-md border">
          Periode:
          <span class="font-medium text-gray-800">
            {{ bulanSekarang }} {{ tahunSekarang }}
          </span>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <form @submit.prevent="submitPayment" class="p-4 space-y-3">
          <div class="md:col-span-2 relative">
            <label class="text-xs font-semibold text-gray-600 uppercase">Nama Siswa</label>
            <div class="relative mt-1">
              <input
                v-model="search"
                :readonly="!!selectedSiswa"
                @input="onSearch"
                @focus="!selectedSiswa && (showDropdownSiswa = true)"
                @keydown.down.prevent="moveDown"
                @keydown.up.prevent="moveUp"
                @keydown.enter.prevent="selectActive"
                class="w-full px-3 py-2 pr-9 rounded-md bg-white border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none text-sm"
                placeholder="Ketik nama siswa..."
              />
              <button
                v-if="selectedSiswa"
                type="button"
                @click="clearSiswa"
                class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 text-sm"
              >✕</button>

              <div
                v-if="showDropdownSiswa && siswaOptions.length && !selectedSiswa"
                class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-60 overflow-auto"
              >
                <div
                  v-for="(siswa, index) in siswaOptions"
                  :key="siswa.id"
                  @click="selectSiswa(siswa)"
                  :class="[
                    'px-3 py-2 cursor-pointer text-sm',
                    index === activeIndex ? 'bg-blue-600 text-white' : 'hover:bg-gray-100'
                  ]"
                >
                  <p class="font-medium">{{ siswa.nama }}</p>
                  <p class="text-xs opacity-70">Kelas {{ siswa.kelas }}</p>
                </div>
              </div>
            </div>
          </div>
          <div class="rounded-md border border-gray-200 p-2">
            <h3 class="font-semibold text-gray-800 mb-3 text-sm flex items-center">📆 Pembayaran Bulanan</h3>
            <div class="grid gap-1">
              <div v-for="item in monthlyItems" :key="item.biaya_sekolah_id" class="flex flex-col sm:flex-row sm:items-center border border-gray-200 rounded-lg p-2 bg-white">
                <div class="flex items-center gap-2 flex-1 min-w-0">
                  <div class="h-9 w-9 bg-gray-100 rounded-md flex items-center justify-center text-gray-600 font-bold shrink-0">
                    {{ item.kategori[0] }}
                  </div>
                  <div class="min-w-0">
                    <p class="font-semibold text-gray-700 truncate text-sm">{{ item.kategori }}</p>
                    <p class="text-[11px] text-gray-500">
                      {{ format(item.nominal_tagihan) }} / bln • Sisa :
                      <span class="text-red-600 font-semibold">{{ item.outstanding }} bln</span>
                    </p>
                  </div>
                </div>
                <div class="flex items-center gap-3 bg-gray-50 p-2 rounded-md border border-gray-200 mt-2 sm:mt-0 sm:ml-2">
                  <div class="flex items-center gap-1">
                    <input type="number" min="0" :max="item.outstanding" v-model.number="item.payMonths" class="w-12 bg-transparent text-center font-semibold text-blue-600 outline-none text-sm" />
                    <span class="text-[10px] font-semibold text-gray-400">BLN</span>
                  </div>
                  <div class="h-5 w-px bg-gray-300"></div>
                  <div class="text-right min-w-[100px]">
                    <p class="text-sm font-bold text-gray-800">{{ format(item.payMonths * item.nominal_tagihan) }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <details class="group rounded-md border border-gray-200 overflow-hidden">
            <summary class="list-none p-2 flex justify-between items-center cursor-pointer hover:bg-gray-50">
              <h2 class="text-gray-700 font-semibold text-sm">📦 Pembayaran Lainnya</h2>
              <span class="text-gray-500">+</span>
            </summary>

            <div class="grid gap-1 border-t border-gray-100 p-2 bg-gray-50">
              <div v-for="item in otherItems" :key="item.biaya_sekolah_id" class="flex flex-col sm:flex-row sm:items-center border border-gray-200 rounded-lg p-2 bg-white">
                <div class="flex items-center gap-2 flex-1 min-w-0">
                  <div class="h-9 w-9 bg-gray-100 rounded-md flex items-center justify-center text-gray-600 font-bold shrink-0">
                    {{ item.kategori[0] }}
                  </div>
                  <div class="min-w-0">
                    <p class="font-semibold text-gray-700 truncate text-sm">{{ item.kategori }}</p>
                    <p class="text-[11px] text-gray-500">
                      Sisa :
                      <span class="text-red-600 font-semibold">{{ format(item.remaining) }}</span>
                    </p>
                  </div>
                </div>
                <div class="flex items-center gap-1">
                  <CurrencyInput v-model="item.amount" :max="item.remaining" />
                </div>
              </div>
            </div>
          </details>

          <div class="sticky bottom-0 bg-white border-t border-gray-200 p-4 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
              <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                Metode:
              </label>
              <select
                v-model="form.metode"
                class="w-32 px-2 py-1.5 bg-gray-50 border border-gray-300 rounded-md text-sm font-medium focus:ring-2 focus:ring-blue-500 outline-none cursor-pointer"
              >
                <option value="tunai">Tunai</option>
                <option value="transfer">Transfer</option>
                <option value="qris">QRIS</option>
              </select>
            </div>

            <div class="flex items-center gap-8 lg:gap-12">
              <div class="flex items-center gap-3">
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Total :</p>
                <p class="text-xl font-black text-blue-600 whitespace-nowrap">
                  {{ format(totalAllocated) }}
                </p>
              </div>
              
              <button 
                type="submit" 
                class="bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-bold px-6 py-2.5 rounded-lg text-sm shadow-md transition-all whitespace-nowrap"
              >
                Simpan Pembayaran
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="lg:col-span-1">
      <div class="mb-3">
        <h2 class="text-lg font-semibold text-gray-800">Riwayat Transaksi</h2>
      </div>

      <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="px-3 py-3 font-semibold text-gray-600">Siswa</th>
                <th class="px-3 py-3 font-semibold text-gray-600">Jumlah</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="p in recentPayments" :key="p.id" class="hover:bg-gray-50">
                <td class="px-3 py-3">
                  <p class="font-medium text-gray-800">{{ p.siswa.nama }}</p>
                  <p class="text-[10px] text-gray-500">{{ p.tanggal_bayar }} • {{ p.metode }}</p>
                </td>
                <td class="px-3 py-3 font-semibold text-blue-600 text-right">{{ format(p.total_bayar) }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="p-3 border-t border-gray-200 bg-white flex justify-between items-center gap-2">
          <button @click="fetchData(pagination.prev_page_url)" :disabled="!pagination.prev_page_url" class="p-1.5 border border-gray-300 rounded-md text-xs disabled:opacity-50">←</button>
          <span class="text-[10px] text-gray-500">{{ pagination.from }}-{{ pagination.to }} dari {{ pagination.total }}</span>
          <button @click="fetchData(pagination.next_page_url)" :disabled="!pagination.next_page_url" class="p-1.5 border border-gray-300 rounded-md text-xs disabled:opacity-50">→</button>
        </div>
      </div>
    </div>

  </div>
</template>


<script setup>
import { ref, watch, onMounted, computed } from 'vue'
import axios from 'axios'
import CurrencyInput from '../components/CurrencyInput.vue'; // 1. Import

const search = ref('')
const recentPayments = ref([])
const bulanSekarang = ref('Desember')
const tahunSekarang = ref('2025')
const loading = ref(false)
const loadingTagihan = ref(false)
const siswaOptions = ref([]);
const selectedSiswa = ref(null);
const showDropdownSiswa = ref(false);
const monthlyItems = ref([])
const otherItems = ref([])
const activeIndex = ref(-1) 

let searchTimer = null;

const showNonMonthly = ref(false)

const form = ref({
  siswa_id: null,
  tagihan: '',
  jumlah: '',
  metode: ''
})

const initialForm = {
  siswa_id: null,
  kelas_id: null,
  nama_siswa: '',
  tanggal: '',
  nominal: 0,
  metode: '',
}

const totalAllocated = computed(() => {

  // 1. Hitung total monthly (pastikan ada isinya)
  const monthlyTotal = (monthlyItems.value || []).reduce((sum, item) => {
    const qty = item.payMonths || 0;
    const nominal = item.nominal_tagihan || 0;
    return sum + (qty * nominal);
  }, 0);

  // 2. Hitung total non-monthly (pastikan ada isinya)
  const nonMonthlyTotal = (otherItems.value || []).reduce((sum, item) => {
    // Hanya jumlahkan jika item dipilih (selected)
    return sum + (item.amount || 0);
  }, 0);


  return monthlyTotal + nonMonthlyTotal
})

// 3. Methods (Fungsi Biasa)
const format = (value) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0
  }).format(value)
}

const fetchSiswa = async () => {
  loading.value = true;
  try {
    const res = await fetch(
      `/api/v1/siswa/search?q=${encodeURIComponent(search.value)}`
    );
    const json = await res.json()
    siswaOptions.value = json.data || []
  } catch (e) {
    console.error(e);
    siswaOptions.value = [];
  } finally {
    loading.value = false;
  }
};

const onSearch = () => {
  if (searchTimer) clearTimeout(searchTimer);
  if (search.value.length < 2) return;

  searchTimer = setTimeout(() => {
    fetchSiswa();
  }, 500);
};

const selectSiswa = async (siswa) => {
  selectedSiswa.value = siswa;
  search.value = `${siswa.nama} — Kelas ${siswa.kelas}`
  showDropdownSiswa.value = false;
  activeIndex.value = -1
  // Simpan ke form
  form.value.siswa_id = siswa.id;
  form.value.kelas_aktif_id = siswa.kelas_aktif_id;
  await fetchTagihanSiswa()
};

const clearSiswa = () => {
  selectedSiswa.value = null;
  search.value = '';
  form.value.siswa_id = null;
  siswaOptions.value = [];
  showDropdownSiswa.value = false
  monthlyItems.value = []
  otherItems.value = []

};

const fetchTagihanSiswa = async () => {
  loadingTagihan.value = true
  try {
    const res = await fetch(
      `/api/v1/keuangan/pembayaran/autofill/${form.value.siswa_id}`
    )
    const json = await res.json()
    monthlyItems.value = (json.bulanan || []).map(item => ({
      ...item,
      payMonths: 0 // ← input user, frontend only
    }))
    otherItems.value = (json.non_bulanan || []).map(item => ({
      ...item,
      amount: 0 // ← input user
    }))
    
  } catch (e) {
    console.error(e)
    monthlyItems.value = []
    otherItems.value = []
  } finally {
    loadingTagihan.value = false
  }
}

const fetchData = async (url = '/api/v1/keuangan/pembayaran?page=1') => {
  loading.value = true
  try {
    const response = await axios.get(url)
    recentPayments.value = response.data.data
    pagination.value = {
      current_page: response.data.meta.current_page,
      total: response.data.meta.total,
      from: response.data.meta.from,
      to: response.data.meta.to,
      prev_page_url: response.data.links.prev,
      next_page_url: response.data.links.next
    }
  } catch (error) {
    console.error("Gagal mengambil data pembayaran:", error)
  } finally {
    loading.value = false
  }
}

const formatNominal = (val) => new Intl.NumberFormat('id-ID').format(val)

const namaBulan = [
  '', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
]

const moveDown = () => {
  if (!showDropdownSiswa.value) return
  if (activeIndex.value < siswaOptions.value.length - 1) {
    activeIndex.value++
  }
}

const moveUp = () => {
  if (!showDropdownSiswa.value) return
  if (activeIndex.value > 0) {
    activeIndex.value--
  }
}

const selectActive = () => {
  if (activeIndex.value >= 0) {
    selectSiswa(siswaOptions.value[activeIndex.value])
  }
}

const formatPeriode = (payment) => {
  // pastikan hanya SPP
  if (payment.biaya_sekolah?.kategori !== 'SPP') {
    return payment.biaya_sekolah.kategori
  }

  if (!payment.bulan_tagihan || !payment.tahun_tagihan) {
    return '-'
  }

  return `${namaBulan[payment.bulan_tagihan]} ${payment.tahun_tagihan}`
}

const submitPayment = async () => {
  loading.value = true
  try {
    await axios.post('/api/v1/keuangan/pembayaran', buildPayload())
    alert('Pembayaran berhasil disimpan')
    await fetchData()
  } catch (error) {
    console.error("Gagal mengambil data pembayaran:", error)
  } finally {
    loading.value = false
  }
}

const buildPayload = () => {
  const bulananPayload = monthlyItems.value
    .filter(i => i.payMonths > 0)
    .map(i => ({
      biaya_sekolah_id: i.biaya_sekolah_id,
      jumlah_bulan: i.payMonths
  }))

  const nonBulananPayload = otherItems.value
    .filter(i => i.amount > 0)
    .map(i => ({
      biaya_sekolah_id: i.biaya_sekolah_id,
      nominal: i.amount
  }))

  const payload = {
    siswa_id: form.value.siswa_id,
    kelas_aktif_id: form.value.kelas_aktif_id,
    metode: form.value.metode,
    total_bayar: totalAllocated.value,
    pembayaran: {
      bulanan: bulananPayload,
      non_bulanan: nonBulananPayload
    }
  }

  return payload
}

const pagination = ref({
  current_page: 1,
  total: 0,
  from: 0,
  to: 0,
  prev_page_url: null,
  next_page_url: null
})

onMounted(fetchData)

</script>