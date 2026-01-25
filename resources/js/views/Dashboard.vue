<template>
  <div class="min-h-screen bg-gray-100">
    <div class="max-w-7xl mx-auto space-y-2">

    <!-- Header -->
      <div>
          <h1 class="text-2xl font-semibold text-gray-800">Laporan Bulanan SPP</h1>
          <p class="text-sm text-gray-500">SD Tahfizhul Quran Mutiara Islam • Januari 2026</p>
      </div>
      <div class="bg-white p-6 rounded shadow border-l-4 border-blue-600">
        <h2 class="text-lg font-semibold mb-4">Total Grand</h2>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <p class="text-sm text-gray-500">Tagihan</p>
            <p class="text-xl font-semibold">
              {{ rupiah(grand.total_tagihan) }}
            </p>
          </div>

          <div>
            <p class="text-sm text-gray-500">Dibayar</p>
            <p class="text-xl font-semibold text-green-600">
              {{ rupiah(grand.total_dibayar) }}
            </p>

            <div class="mt-1 text-xs text-gray-500 space-y-0.5">
              <p>Tunai: {{ rupiah(grand.dibayar_tunai) }}</p>
              <p>Transfer: {{ rupiah(grand.dibayar_transfer) }}</p>
            </div>
          </div>

          <div>
            <p class="text-sm text-gray-500">Sisa</p>
            <p class="text-xl font-semibold text-red-600">
              {{ rupiah(grand.total_sisa) }}
            </p>
          </div>
        </div>
      </div>
      <!-- Filter -->
      <!-- <div class="bg-white p-4 rounded-lg shadow flex flex-wrap gap-4 items-end">
          <div>
          <label class="block text-sm text-gray-600 mb-1">Bulan</label>
          <select class="border rounded px-3 py-2">
              <option>Januari</option>
              <option>Februari</option>
          </select>
          </div>

          <div>
          <label class="block text-sm text-gray-600 mb-1">Tahun</label>
          <select class="border rounded px-3 py-2">
              <option>2026</option>
              <option>2025</option>
          </select>
          </div>

          <div>
          <label class="block text-sm text-gray-600 mb-1">Kelas</label>
          <select class="border rounded px-3 py-2">
              <option>Semua</option>
              <option>3A</option>
              <option>3B</option>
          </select>
          </div>

          <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
          Tampilkan
          </button>
      </div> -->

      <!-- Summary -->
      <!-- <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="bg-white p-4 rounded shadow">
          <p class="text-sm text-gray-500">Total Tagihan</p>
          <p class="text-xl font-semibold">Rp 15.000.000</p>
          </div>

          <div class="bg-white p-4 rounded shadow">
          <p class="text-sm text-gray-500">Sudah Dibayar</p>
          <p class="text-xl font-semibold text-green-600">Rp 12.000.000</p>
          </div>

          <div class="bg-white p-4 rounded shadow">
          <p class="text-sm text-gray-500">Belum Dibayar</p>
          <p class="text-xl font-semibold text-red-600">Rp 3.000.000</p>
          </div>

          <div class="bg-white p-4 rounded shadow">
          <p class="text-sm text-gray-500">Pelunasan</p>
          <p class="text-xl font-semibold">80%</p>
          </div>
      </div> -->


      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <RekapTable
          title="Rekap Bulanan"
          :items="rekap.bulanan"
        />

        <RekapTable
          title="Rekap Tahunan"
          :items="rekap.tahunan"
        />

        <!-- <div class="bg-white p-4 rounded-lg shadow">
          <h2 class="text-lg font-semibold mb-4">Rekap Pembayaran Bulanan</h2>

          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-3 text-left">Item</th>
                  <th class="px-4 py-3 text-right">Total Tagihan</th>
                  <th class="px-4 py-3 text-right">Dibayar</th>
                  <th class="px-4 py-3 text-right">Sisa</th>
                </tr>
              </thead>
              <tbody class="divide-y">
                <tr>
                  <td class="px-4 py-2">SPP</td>
                  <td class="px-4 py-2 text-right">9.000.000</td>
                  <td class="px-4 py-2 text-right">7.500.000</td>
                  <td class="px-4 py-2 text-right text-red-600">1.500.000</td>
                </tr>
                <tr>
                  <td class="px-4 py-2">Komite</td>
                  <td class="px-4 py-2 text-right">3.000.000</td>
                  <td class="px-4 py-2 text-right">3.000.000</td>
                  <td class="px-4 py-2 text-right">0</td>
                </tr>
                <tr>
                  <td class="px-4 py-2">Ekstrakurikuler</td>
                  <td class="px-4 py-2 text-right">2.000.000</td>
                  <td class="px-4 py-2 text-right">1.500.000</td>
                  <td class="px-4 py-2 text-right text-red-600">500.000</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div> -->

        <!-- <div class="bg-white p-4 rounded-lg shadow">
          <h2 class="text-lg font-semibold mb-4">Rekap Pembayaran Tahunan</h2>

          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-3 text-left">Item</th>
                  <th class="px-4 py-3 text-right">Total Tagihan</th>
                  <th class="px-4 py-3 text-right">Dibayar</th>
                  <th class="px-4 py-3 text-right">Sisa</th>
                </tr>
              </thead>
              <tbody class="divide-y">
                <tr>
                  <td class="px-4 py-2">Sarana & Prasarana</td>
                  <td class="px-4 py-2 text-right">5.000.000</td>
                  <td class="px-4 py-2 text-right">4.000.000</td>
                  <td class="px-4 py-2 text-right text-red-600">1.000.000</td>
                </tr>
                <tr>
                  <td class="px-4 py-2">Buku</td>
                  <td class="px-4 py-2 text-right">2.000.000</td>
                  <td class="px-4 py-2 text-right">2.000.000</td>
                  <td class="px-4 py-2 text-right">0</td>
                </tr>
                <tr>
                  <td class="px-4 py-2">Ujian</td>
                  <td class="px-4 py-2 text-right">1.500.000</td>
                  <td class="px-4 py-2 text-right">1.200.000</td>
                  <td class="px-4 py-2 text-right text-red-600">300.000</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div> -->
      </div>
      <!-- Actions -->
      <div class="flex justify-end gap-2">
          <button class="px-4 py-2 border rounded hover:bg-gray-50">Export Excel</button>
          <button class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-900">
          Cetak PDF
          </button>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import RekapTable from '@/components/RekapTable.vue'


/* ---------- STATE ---------- */
const periode = ref({
  bulan: null,
  tahun: null,
  label: ''
})

const rekap = ref({
  bulanan: [],
  tahunan: []
})

const grand = ref({
  total_tagihan: 0,
  total_dibayar: 0,
  total_sisa: 0
})

/* ---------- FETCH ---------- */
const fetchData = async () => {
  const res = await fetch(
    '/api/v1/keuangan/laporan/rekap-bulanan?tahun_ajaran_id=1&bulan=1&tahun=2026'
  )
  const json = await res.json()
  
  periode.value = json.periode
  rekap.value = json.rekap
  grand.value = json.grand_total
}

onMounted(fetchData)

/* ---------- HELPER ---------- */
const rupiah = (n) =>
  Number(n || 0).toLocaleString('id-ID')
</script>