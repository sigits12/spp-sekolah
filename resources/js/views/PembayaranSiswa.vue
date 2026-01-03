<template>
  <div class="flex justify-between items-center mb-6">
    <div>
      <h2 class="text-2xl font-bold text-gray-800">Pembayaran</h2>
      <p class="text-sm text-gray-500">Periode: {{ bulanSekarang }} {{ tahunSekarang }}</p>
    </div>
  </div>

  <div class="bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden">
    <div class="bg-indigo-600 px-6 py-4"></div>
    <form @submit.prevent="submitPayment" class="p-6 space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- <div class="md:col-span-2">
          <label class="text-xs font-bold text-slate-600 uppercase">Nama Penyetor</label>
          <input v-model="form.name"
            class="w-full mt-1 px-4 py-3 rounded-xl bg-slate-50 border focus:border-indigo-500 outline-none"
            placeholder="Nama..." />
        </div> -->
        <div class="md:col-span-2 relative">
  <label class="text-xs font-bold text-slate-600 uppercase">
    Nama Siswa
  </label>
<div class="relative mt-1">
  <input
    v-model="search"
    :readonly="!!selectedSiswa"
    @input="onSearch"
    @focus="!selectedSiswa && (showDropdownSiswa = true)"
    @keydown.down.prevent="moveDown"
    @keydown.up.prevent="moveUp"
    @keydown.enter.prevent="selectActive"
    class="w-full px-4 py-3 pr-10 rounded-xl bg-slate-50 border focus:border-indigo-500 outline-none"
    placeholder="Ketik nama siswa..."
  />

  <button
      v-if="selectedSiswa"
      type="button"
      @click="clearSiswa"
      class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500"
    >
      ✕
    </button>

  <div
    v-if="showDropdownSiswa && siswaOptions.length && !selectedSiswa"
    class="absolute z-50 w-full mt-1 bg-white border rounded-xl shadow-lg max-h-60 overflow-auto"
  >
    <div
      v-for="(siswa, index) in siswaOptions"
      :key="siswa.id"
      @click="selectSiswa(siswa)"
      :class="['px-4 py-3 hover:bg-indigo-50 cursor-pointer',
        index === activeIndex
          ? 'bg-indigo-600 text-white'
          : 'hover:bg-indigo-50'
      ]"
    >
      <p class="font-medium text-slate-700">
        {{ siswa.nama }}
      </p>
      <p class="text-xs text-slate-500">
        Kelas {{ siswa.kelas }}
      </p>
    </div>
  </div>
</div>
</div>

        <div>
          <label class="text-xs font-bold text-slate-600 uppercase">Total Dibayar</label>
          <input v-model.number="form.total"
            class="w-full mt-1 px-4 py-3 rounded-xl bg-indigo-50 text-indigo-700 font-bold text-lg border border-indigo-200 outline-none" />
        </div>

        <div>
          <label class="text-xs font-bold text-slate-600 uppercase">Metode</label>
          <select v-model="form.metode"
            class="w-full mt-1 px-4 py-3 rounded-xl bg-slate-50 border outline-none">
            <option value="tunai">Tunai</option>
            <option value="transfer">Transfer</option>
            <option value="qris">QRIS</option>
          </select>
        </div>
      </div>

      <div class="rounded-xl border p-4">
        <h3 class="font-semibold text-slate-800 mb-4 flex items-center">📆 Pembayaran Bulanan</h3>
      <!-- <pre>{{ JSON.stringify(monthlyItems, null, 2) }}</pre> -->
        <div class="grid gap-3">
          
          <div v-for="item in monthlyItems" :key="item.biaya_sekolah_id" class="flex items-center bg-white border border-slate-200 rounded-2xl p-2 shadow-sm">
              
            <div class="flex items-center gap-3 flex-1 min-w-0">
              <div class="h-10 w-10 bg-amber-50 rounded-lg flex items-center justify-center text-amber-600 font-bold italic shrink-0 text-lg">{{ item.kategori[0] }}</div>
              <div class="min-w-0">
                  <p class="font-bold text-slate-700 truncate text-sm">{{ item.kategori }}</p>
                  <p class="text-[10px] text-slate-500 whitespace-nowrap">
                    {{ format(item.nominal_tagihan) }} / bln • Sisa : <span class="text-red-600 font-bold text-[9px]">{{ item.outstanding }} BLN</span>
                  </p>
                </div>
              </div>

              <div class="flex items-center gap-3 bg-slate-50 p-2 rounded-xl border border-slate-100 ml-2">
                <div class="flex items-center gap-1">
                  <input 
                    type="number" 
                    min="0" 
                    :max="item.outstanding"
                    v-model.number="item.payMonths"
                    class="w-12 bg-transparent text-center font-bold text-indigo-600 outline-none text-lg" 
                    value="0"
                  />
                  <span class="text-[9px] font-bold text-gray-400 tracking-tighter">BLN</span>
                </div>

                <div class="h-6 w-px bg-slate-200 shrink-0"></div>

                <div class="text-right min-w-[160px]">
                  <p class="text-lg font-black font-bold text-slate-800 tracking-tight">
                    {{ format(item.payMonths * item.nominal_tagihan) }}
                  </p>
                </div>
              </div>
          </div>
        </div>
      </div>

      <details class="group rounded-xl border overflow-hidden">
        <summary class="list-none p-5 flex justify-between items-center cursor-pointer hover:bg-gray-50 transition-colors">
          <div class="text-left">
            <h2 class="text-slate-700 font-semibold">📦 Pembayaran Lainnya</h2>
          </div>
          <div class="relative flex items-center justify-center w-6 h-6">
            <div class="absolute w-4 h-0.5 bg-black rounded-full"></div>
            <div class="absolute w-0.5 h-4 bg-black rounded-full group-open:rotate-90 group-open:opacity-0"></div>
          </div>
        </summary>

        <div class="px-6 pb-6 space-y-3 border-t border-gray-100 pt-4 bg-gray-50">

          <div v-for="item in otherItems" :key="item.biaya_sekolah_id" class="flex items-center justify-between bg-white border border-slate-200 rounded-2xl p-2">
            <div class="flex items-center gap-4">
              <div class="h-10 w-10 bg-amber-50 rounded-lg flex items-center justify-center text-amber-600 font-bold italic">{{ item.kategori[0] }}</div>
                <p class="font-bold text-slate-700">{{ item.kategori }}</p>
                <p class="text-xs text-slate-500">
                  Sisa : 
                  <span class="text-red-600 font-semibold"> 
                    {{ format(item.remaining) }}
                  </span>
                </p>
            </div>
            <div class="relative">
              <CurrencyInput
                v-model="item.amount"
                :max="item.remaining"
              />
            </div>
          </div>
        
        </div>
      </details>

      <div class="sticky bottom-0 bg-indigo-600 rounded-xl p-5 flex justify-between items-center text-white">
        <div>
          <p class="text-sm">Total Dialokasikan</p>
          <p class="text-xl font-bold">{{ format(totalAllocated) }}</p>
        </div>

          <!-- :disabled="totalAllocated !== form.total" -->
        <button
          type="submit"
          class="bg-white text-indigo-600 font-bold px-6 py-3 rounded-xl disabled:opacity-50">
          Simpan Pembayaran
        </button>
      </div>

    </form>
  </div>

  <div class="mt-8 mb-6">
    <div class="flex items-center justify-between">
      <h2 class="text-2xl font-bold text-slate-800">Riwayat Transaksi Terakhir</h2>
      <div class="h-1 flex-grow mx-4 bg-slate-200 rounded-full"></div>
    </div>
  </div>
  
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <table class="w-full text-left border-collapse">
      <thead class="bg-gray-50 border-b border-gray-200">
        <tr>
          <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Tanggal</th>
          <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Siswa</th>
          <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Kelas</th>
          <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Metode</th>
          <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Jumlah</th>
          <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        <tr v-for="p in recentPayments" :key="p.id" class="border-t border-gray-700">
          <td class="px-4 py-3">{{ p.tanggal_bayar }}</td>
          <td class="px-4 py-3">{{ p.siswa.nama }}</td>
          <td class="px-4 py-3">{{ p.siswa.kelas }}</td>
          <td class="px-4 py-3">{{ p.metode }}</td>
          <td class="px-4 py-3">{{ format(p.total_bayar) }}</td>
        </tr>
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