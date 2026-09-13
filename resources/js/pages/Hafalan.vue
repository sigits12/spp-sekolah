<template>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-2">
    <div class="lg:col-span-2">
      <div class="flex items-center justify-between mb-2">
        <h1 class="text-xl font-semibold text-gray-800">
          Hafalan
        </h1>
      </div>

      <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <form @submit.prevent="simpanSetoran" class="p-4 space-y-3">
          <SiswaSearchInput @select-siswa="onSiswaSelected" />

          <div class="rounded-md border border-gray-200 p-2">
            <h3 class="font-semibold text-gray-800 mb-3 text-sm flex items-center">📖 Setoran Hafalan</h3>
            <div class="grid gap-2">
              <div class="flex flex-col sm:flex-row sm:items-center border border-gray-200 rounded-lg p-2 bg-white gap-2">
                <div class="flex-1 min-w-0">
                  <p class="font-semibold text-gray-700 truncate text-sm">Jenis Setoran</p>
                  <p class="text-[11px] text-gray-500">Ziyadah atau murojaah</p>
                </div>
                <select
                  v-model="form.jenis"
                  class="w-full sm:w-40 px-2 py-1.5 bg-gray-50 border border-gray-300 rounded-md text-sm font-medium focus:ring-2 focus:ring-blue-500 outline-none"
                >
                  <option value="Ziyadah">Ziyadah</option>
                  <option value="Murojaah">Murojaah</option>
                </select>
              </div>

              <div class="flex flex-col sm:flex-row sm:items-center border border-gray-200 rounded-lg p-2 bg-white gap-2">
                <div class="flex-1 min-w-0">
                  <p class="font-semibold text-gray-700 truncate text-sm">Surah</p>
                  <p class="text-[11px] text-gray-500">Nama surah yang disetorkan</p>
                </div>
                <input
                  v-model="form.surah"
                  type="text"
                  placeholder="Al-Baqarah"
                  class="w-full sm:w-48 px-3 py-2 bg-gray-50 border border-gray-300 rounded-md text-sm outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>

              <div class="flex flex-col sm:flex-row sm:items-center border border-gray-200 rounded-lg p-2 bg-white gap-2">
                <div class="flex-1 min-w-0">
                  <p class="font-semibold text-gray-700 truncate text-sm">Ayat</p>
                  <p class="text-[11px] text-gray-500">Rentang ayat setoran</p>
                </div>
                <div class="flex items-center gap-2">
                  <input
                    v-model="form.ayatDari"
                    type="text"
                    placeholder="Dari"
                    class="w-16 px-2 py-2 bg-gray-50 border border-gray-300 rounded-md text-sm text-center outline-none focus:ring-2 focus:ring-blue-500"
                  />
                  <span class="text-xs text-gray-400">—</span>
                  <input
                    v-model="form.ayatSampai"
                    type="text"
                    placeholder="Sampai"
                    class="w-16 px-2 py-2 bg-gray-50 border border-gray-300 rounded-md text-sm text-center outline-none focus:ring-2 focus:ring-blue-500"
                  />
                </div>
              </div>

              <div class="flex flex-col sm:flex-row sm:items-center border border-gray-200 rounded-lg p-2 bg-white gap-2">
                <div class="flex-1 min-w-0">
                  <p class="font-semibold text-gray-700 truncate text-sm">Nilai</p>
                  <p class="text-[11px] text-gray-500">Skor setoran 0 sampai 100</p>
                </div>
                <div class="flex items-center gap-1 bg-gray-50 p-2 rounded-md border border-gray-200">
                  <input
                    type="number"
                    min="0"
                    max="100"
                    v-model.number="form.nilai"
                    class="w-14 bg-transparent text-center font-semibold text-blue-600 outline-none text-sm"
                  />
                  <span class="text-[10px] font-semibold text-gray-400">NILAI</span>
                </div>
              </div>
            </div>
          </div>

          <details class="group rounded-md border border-gray-200 overflow-hidden">
            <summary class="list-none p-2 flex justify-between items-center cursor-pointer hover:bg-gray-50">
              <h2 class="text-gray-700 font-semibold text-sm">📝 Catatan Guru</h2>
              <span class="text-gray-500">+</span>
            </summary>
            <div class="border-t border-gray-100 p-2 bg-gray-50">
              <textarea
                v-model="form.catatan"
                rows="3"
                placeholder="Catatan kelancaran, tajwid, atau target berikutnya..."
                class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-sm outline-none focus:ring-2 focus:ring-blue-500"
              ></textarea>
            </div>
          </details>

          <div class="sticky bottom-0 bg-white border-t border-gray-200 p-4 flex items-center justify-between gap-4">
            <div class="min-w-0">
              <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Siswa</p>
              <p class="text-sm font-semibold text-gray-800 truncate">
                {{ selectedSiswa ? `${selectedSiswa.nama} · Kelas ${selectedSiswa.kelas || '-'}` : 'Belum dipilih' }}
              </p>
            </div>
            <button
              type="submit"
              :disabled="isSubmitting || !selectedSiswa"
              class="bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-bold px-6 py-2.5 rounded-lg text-sm shadow-md transition-all whitespace-nowrap disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Simpan Setoran
            </button>
          </div>
        </form>

        <div class="w-full bg-white rounded-xl border border-slate-200 shadow-sm p-4 md:p-6">
          <h3 class="text-lg font-bold text-slate-800 mb-4">Histori Setoran</h3>

          <div v-if="!selectedSiswa" class="py-8 text-center text-sm text-slate-500 border border-dashed border-slate-200 rounded-lg">
            Pilih siswa untuk melihat histori setoran.
          </div>

          <div v-else-if="historyList.length === 0" class="py-8 text-center text-sm text-slate-500 border border-dashed border-slate-200 rounded-lg">
            Belum ada riwayat setoran untuk siswa ini.
          </div>

          <div v-else class="overflow-x-auto rounded-lg border border-slate-200">
            <table class="w-full text-left text-sm text-slate-600 border-collapse">
              <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 border-b border-slate-200">
                <tr>
                  <th class="px-2 py-1 font-semibold">Tanggal</th>
                  <th class="px-2 py-1 font-semibold">Jenis</th>
                  <th class="px-2 py-1 font-semibold">Nilai</th>
                  <th class="px-2 py-1 font-semibold">Surah</th>
                  <th class="px-2 py-1 font-semibold">Ayat</th>
                  <th class="px-2 py-1 font-semibold">Catatan</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-200 bg-white">
                <tr
                  v-for="item in historyList"
                  :key="item.id"
                  class="hover:bg-slate-50 transition-colors"
                >
                  <td class="px-2 py-1 whitespace-nowrap text-slate-700 font-medium">{{ item.tanggal }}</td>
                  <td class="px-2 py-1 whitespace-nowrap">
                    <span
                      :class="item.jenis === 'Ziyadah'
                        ? 'bg-blue-50 text-blue-700 border-blue-100'
                        : 'bg-amber-50 text-amber-700 border-amber-100'"
                      class="inline-block px-2.5 py-1 text-xs font-medium rounded-md border"
                    >
                      {{ item.jenis }}
                    </span>
                  </td>
                  <td class="px-2 py-1 whitespace-nowrap font-semibold text-slate-800">{{ item.nilai ?? '-' }}</td>
                  <td class="px-2 py-1 whitespace-nowrap">{{ item.surah }}</td>
                  <td class="px-2 py-1 whitespace-nowrap">{{ item.ayat }}</td>
                  <td class="px-2 py-1">{{ item.catatan || '-' }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="lg:col-span-1">
      <div class="mb-3">
        <h2 class="text-lg font-semibold text-gray-800">Riwayat Setoran</h2>
      </div>

      <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="px-3 py-3 font-semibold text-gray-600">Siswa</th>
                <th class="px-3 py-3 font-semibold text-gray-600 text-right">Nilai</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-if="recentSetoran.length === 0">
                <td colspan="2" class="px-3 py-6 text-center text-xs text-gray-400">Belum ada setoran</td>
              </tr>
              <tr v-for="item in pagedRecent" :key="item.id" class="hover:bg-gray-50">
                <td class="px-3 py-3">
                  <p class="font-medium text-gray-800">{{ item.nama }}</p>
                  <p class="text-[10px] text-gray-500">{{ item.tanggal }} • {{ item.jenis }}</p>
                </td>
                <td class="px-3 py-3 font-semibold text-blue-600 text-right">
                  {{ item.nilai ?? '-' }}
                  <p class="text-[10px] font-normal text-gray-500">{{ item.surah }} {{ item.ayat }}</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="p-3 border-t border-gray-200 bg-white flex justify-between items-center gap-2">
          <button
            @click="prevPage"
            :disabled="page <= 1"
            class="p-1.5 border border-gray-300 rounded-md text-xs disabled:opacity-50"
          >←</button>
          <span class="text-[10px] text-gray-500">{{ pageFrom }}-{{ pageTo }} dari {{ recentSetoran.length }}</span>
          <button
            @click="nextPage"
            :disabled="pageTo >= recentSetoran.length"
            class="p-1.5 border border-gray-300 rounded-md text-xs disabled:opacity-50"
          >→</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import SiswaSearchInput from '../components/SiswaSearchInput.vue'

const STORAGE_KEY = 'hafalan_setoran'

const selectedSiswa = ref(null)
const isSubmitting = ref(false)
const allSetoran = ref([])
const page = ref(1)
const perPage = 8

const form = ref({
  jenis: 'Ziyadah',
  nilai: null,
  surah: '',
  ayatDari: '',
  ayatSampai: '',
  catatan: '',
})

const historyList = computed(() => {
  if (!selectedSiswa.value) return []
  return allSetoran.value.filter((item) => item.siswa_id === selectedSiswa.value.id)
})

const recentSetoran = computed(() => allSetoran.value)

const pageFrom = computed(() => {
  if (!recentSetoran.value.length) return 0
  return (page.value - 1) * perPage + 1
})

const pageTo = computed(() => Math.min(page.value * perPage, recentSetoran.value.length))

const pagedRecent = computed(() => {
  const start = (page.value - 1) * perPage
  return recentSetoran.value.slice(start, start + perPage)
})

const formatAyat = () => {
  if (form.value.ayatDari && form.value.ayatSampai) {
    return `${form.value.ayatDari}–${form.value.ayatSampai}`
  }
  return form.value.ayatDari || form.value.ayatSampai || '-'
}

const resetForm = () => {
  form.value = {
    jenis: 'Ziyadah',
    nilai: null,
    surah: '',
    ayatDari: '',
    ayatSampai: '',
    catatan: '',
  }
}

const loadSetoran = () => {
  try {
    const raw = localStorage.getItem(STORAGE_KEY)
    allSetoran.value = raw ? JSON.parse(raw) : []
  } catch {
    allSetoran.value = []
  }
}

const saveSetoranStore = () => {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(allSetoran.value))
}

const onSiswaSelected = (siswa) => {
  selectedSiswa.value = siswa
  if (!siswa) {
    resetForm()
  }
}

const simpanSetoran = () => {
  if (!selectedSiswa.value || isSubmitting.value) return
  if (!form.value.surah) {
    alert('Surah wajib diisi')
    return
  }
  if (form.value.nilai === null || form.value.nilai === '' || Number.isNaN(form.value.nilai)) {
    alert('Nilai wajib diisi')
    return
  }

  isSubmitting.value = true
  try {
    const record = {
      id: Date.now(),
      siswa_id: selectedSiswa.value.id,
      nama: selectedSiswa.value.nama,
      kelas: selectedSiswa.value.kelas,
      jenis: form.value.jenis,
      nilai: form.value.nilai,
      surah: form.value.surah,
      ayat: formatAyat(),
      catatan: form.value.catatan,
      tanggal: new Date().toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
      }),
    }

    allSetoran.value = [record, ...allSetoran.value]
    saveSetoranStore()
    page.value = 1
    resetForm()
  } finally {
    isSubmitting.value = false
  }
}

const prevPage = () => {
  if (page.value > 1) page.value -= 1
}

const nextPage = () => {
  if (pageTo.value < recentSetoran.value.length) page.value += 1
}

onMounted(loadSetoran)
</script>
