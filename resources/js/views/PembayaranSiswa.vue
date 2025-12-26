<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <div>
        <h2 class="text-2xl font-bold text-gray-800">Pembayaran</h2>
        <p class="text-sm text-gray-500">Periode: {{ bulanSekarang }} {{ tahunSekarang }}</p>
      </div>
    </div>

    <!-- <div class="bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden">
      <div class="bg-indigo-600 px-6 py-4">
        <h2 class="text-white font-semibold text-lg flex items-center">
          <span class="mr-2">💳</span> Formulir Pembayaran Baru
        </h2>
      </div>
        
      <form @submit.prevent="submitPayment" class="p-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700 uppercase tracking-wider">Nama Penyetor</label>
            <input v-model="form.name" type="text" class="w-full px-4 py-3 rounded-xl bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white transition-all outline-none" placeholder="Masukkan nama...">
          </div>
          
          <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700 uppercase tracking-wider">Nominal (IDR)</label>
            <input v-model.number="form.amount" type="number" class="w-full px-4 py-3 rounded-xl bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white transition-all outline-none" placeholder="0">
          </div>

          <div class="flex items-end">
            <button 
            type="submit" 
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-lg shadow-lg shadow-indigo-200 transition duration-300"
          >
            Konfirmasi Pembayaran
          </button>
          </div>
        </div>
      </form>
    </div> -->

    <div class="bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden">
    <div class="bg-indigo-600 px-6 py-4"></div>

  <form @submit.prevent="submitPayment" class="p-6 space-y-6">

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="md:col-span-2">
        <label class="text-xs font-bold text-slate-600 uppercase">Nama Penyetor</label>
        <input v-model="form.name"
          class="w-full mt-1 px-4 py-3 rounded-xl bg-slate-50 border focus:border-indigo-500 outline-none"
          placeholder="Nama..." />
      </div>

      <div>
        <label class="text-xs font-bold text-slate-600 uppercase">Total Dibayar</label>
        <input v-model.number="form.total"
          class="w-full mt-1 px-4 py-3 rounded-xl bg-indigo-50 text-indigo-700 font-bold text-lg border border-indigo-200 outline-none" />
      </div>

      <div>
        <label class="text-xs font-bold text-slate-600 uppercase">Metode</label>
        <select v-model="form.method"
          class="w-full mt-1 px-4 py-3 rounded-xl bg-slate-50 border outline-none">
          <option value="tunai">Tunai</option>
          <option value="transfer">Transfer</option>
          <option value="qris">QRIS</option>
        </select>
      </div>
    </div>

    <div class="rounded-xl border p-4">
      <h3 class="font-semibold text-slate-800 mb-4 flex items-center">📆 Pembayaran Bulanan</h3>

      <div class="grid gap-3">
        <div v-for="item in monthlyItems" :key="item.id" class="flex items-center bg-white border border-slate-200 rounded-2xl p-2 shadow-sm">
            
          <div class="flex items-center gap-3 flex-1 min-w-0">
            <div class="h-10 w-10 bg-amber-50 rounded-lg flex items-center justify-center text-amber-600 font-bold italic shrink-0 text-lg">{{ item.name[0] }}</div>
            <div class="min-w-0">
                <p class="font-bold text-slate-700 truncate text-sm">{{ item.name }}</p>
                <p class="text-[10px] text-slate-500 whitespace-nowrap">
                  {{ format(item.price) }} / bln • Sisa : <span class="text-red-600 font-bold text-[9px]">{{ item.unpaidMonths }} BLN</span>
                </p>
              </div>
            </div>

            <div class="flex items-center gap-3 bg-slate-50 p-2 rounded-xl border border-slate-100 ml-2">
              <div class="flex items-center gap-1">
                <input 
                  type="number" 
                  min="0" 
                  :max="item.unpaidMonths"
                  v-model.number="item.payMonths"
                  class="w-12 bg-transparent text-center font-bold text-indigo-600 outline-none text-lg" 
                  value="0"
                />
                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">Bln</span>
              </div>

              <div class="h-6 w-px bg-slate-200 shrink-0"></div>

              <div class="text-right min-w-[160px]">
                <p class="text-lg font-black font-bold text-slate-800 tracking-tight">
                  {{ format(item.payMonths * item.price) }}
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

        <div v-for="item in nonMonthlyItems" :key="item.id" class="flex items-center justify-between bg-white border border-slate-200 rounded-2xl p-2">
          <div class="flex items-center gap-4">
            <div class="h-10 w-10 bg-amber-50 rounded-lg flex items-center justify-center text-amber-600 font-bold italic">{{ item.name[0] }}</div>
              <p class="font-bold text-slate-700">{{ item.name }}</p>
              <p class="text-xs text-slate-500">
                Sisa : 
                <span class="text-red-600 font-semibold"> 
                  {{ format(item.remaining) }}
                </span>
              </p>
          </div>
          <div class="relative">
            <span class="absolute left-3 top-2 font-bold text-slate-700">Rp</span>
            <input 
              type="text" 
              v-model="cicilan"
              class="text-right pl-10 w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none font-bold text-slate-700 text-lg"
              placeholder="0"
            />
          </div>
        </div>
      
      </div>
    </details>

    <div class="rounded-xl border p-5">
      <button type="button"
        @click="showNonMonthly = !showNonMonthly"
        class="w-full flex justify-between items-center font-semibold text-slate-700">
        📦 Pembayaran Lainnya
        <span>{{ showNonMonthly ? '−' : '+' }}</span>
      </button>

      <div v-if="showNonMonthly" class="mt-4 space-y-3">
        <div v-for="item in nonMonthlyItems" :key="item.id"
          class="flex items-center justify-between bg-slate-50 rounded-lg p-4">

          <div class="flex items-center gap-3">
            <input type="checkbox" v-model="item.selected" />
            <div>
              <p class="font-medium">{{ item.name }}</p>
              <p class="text-xs text-slate-500">
                Sisa {{ format(item.remaining) }}
              </p>
            </div>
          </div>

          <input type="number"
            v-model.number="item.amount"
            :disabled="!item.selected"
            class="w-32 rounded-lg border focus:border-indigo-500 outline-none" />
        </div>
      </div>
    </div>

    <div class="sticky bottom-0 bg-indigo-600 rounded-xl p-5 flex justify-between items-center text-white">
      <div>
        <p class="text-sm">Total Dialokasikan</p>
        <p class="text-xl font-bold">{{ format(totalAllocated) }}</p>
      </div>

      <button
        type="submit"
        :disabled="totalAllocated !== form.total"
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
            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Biaya</th>
            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Periode</th>
            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Metode</th>
            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Jumlah</th>
            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Aksi</th>
          </tr>
        </thead>
        <!-- <pre>{{ JSON.stringify(recentPayments, null, 2) }}</pre> -->
        <tbody class="divide-y divide-gray-100">
          <tr v-for="p in recentPayments" :key="p.id" class="border-t border-gray-700">
            <td class="px-4 py-3">{{ p.tanggal_bayar }}</td>
            <td class="px-4 py-3">{{ p.tagihan.siswa.nama }}</td>
            <td class="px-4 py-3">Rp {{ formatNominal(p.jumlah_bayar) }}</td>
            <td class="px-4 py-3">{{ formatPeriode(p.tagihan) }}</td>
            <td class="px-4 py-3">{{ p.metode }}</td>
            <td class="px-4 py-3">Rp {{ formatNominal(p.tagihan.biaya_sekolah.nominal) }}</td>
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
  </div>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue'
import axios from 'axios'

const search = ref('')
const recentPayments = ref([])
const bulanSekarang = ref('Desember')
const tahunSekarang = ref('2025')
const loading = ref(false)

const showNonMonthly = ref(false)

const form = ref({
  siswa: '',
  tagihan: '',
  jumlah: '',
  metode: ''
})

const monthlyItems = ref([
  { id: 1, name: 'SPP', price: 150000, unpaidMonths: 2, payMonths: 0 },
  { id: 2, name: 'Ekskul', price: 20000, unpaidMonths: 1, payMonths: 0 },
  { id: 2, name: 'Komite', price: 5000, unpaidMonths: 1, payMonths: 0 },
])

// 1. Data asli dalam bentuk angka murni
const rawValue = ref(0);

// 2. Computed property untuk memformat tampilan
const cicilan = computed({
  get() {
    // Mengubah angka 500000 -> "500.000"
    return rawValue.value.toLocaleString('id-ID');
  },
  set(newValue) {
    // Mengambil input string, hapus semua karakter non-angka
    // "500.000" -> "500000" -> 500000
    const number = Number(newValue.replace(/[^0-9]/g, ''));
    rawValue.value = number;
  }
});

const nonMonthlyItems = ref([
  { id: 3, name: 'Buku', remaining: 200000, selected: false, amount: 0 },
  { id: 4, name: 'Seragam', remaining: 150000, selected: false, amount: 0 }
])

// 2. Computed Properties
const totalAllocated = computed(() => {
  const monthlyTotal = monthlyItems.value.reduce(
    (sum, i) => sum + (i.payMonths * i.price), 0
  )

  const nonMonthlyTotal = nonMonthlyItems.value.reduce(
    (sum, i) => i.selected ? sum + i.amount : sum, 0
  )

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

const submitPayment = () => {
  alert('Pembayaran siap disimpan (logic backend menyusul)')
  console.log('Data yang dikirim:', { 
    form, 
    monthly: monthlyItems.value, 
    nonMonthly: nonMonthlyItems.value 
  })
}

const fetchData = async (url = '/api/v1/keuangan/pembayaran?page=1') => {
  loading.value = true
  try {
    const response = await axios.get(url)
    recentPayments.value = response.data.data
    pagination.value = {
      current_page: response.data.current_page,
      total: response.data.total,
      from: response.data.from,
      to: response.data.to,
      prev_page_url: response.data.prev_page_url,
      next_page_url: response.data.next_page_url
    }
  } catch (error) {
    console.error("Gagal mengambil data pembayaran:", error)
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

const namaBulan = [
  '', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
]


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