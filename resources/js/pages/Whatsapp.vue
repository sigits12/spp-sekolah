<template>
  <div>
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
      <div>
        <h2 class="text-2xl font-bold text-gray-800">Nomor WhatsApp</h2>
        <p class="text-sm text-gray-500">Kelola nomor WhatsApp untuk pengiriman notifikasi</p>
      </div>
      <button
        @click="openCreate"
        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white font-semibold px-4 py-2 rounded-lg text-sm shadow-sm transition"
      >
        <span class="text-base leading-none">+</span> Tambah Nomor
      </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div class="flex items-center gap-2 px-4 py-3 border-b border-gray-200">
        <input
          v-model="search"
          type="text"
          placeholder="Cari nama / nomor..."
          class="w-full max-w-sm px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-500"
        />
        <span v-if="loading" class="ml-auto text-sm text-indigo-600 animate-pulse">Memuat...</span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Nama</th>
              <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Nomor</th>
              <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Status</th>
              <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-if="loading">
              <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-400">Memuat data...</td>
            </tr>
            <tr v-else-if="nomorList.length === 0">
              <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-400">
                Belum ada data. Klik "Tambah Nomor" untuk menambahkan.
              </td>
            </tr>
            <tr v-for="item in nomorList" :key="item.id" class="hover:bg-gray-50 transition">
              <td class="px-6 py-4">
                <p class="font-semibold text-gray-800">{{ item.name }}</p>
              </td>
              <td class="px-6 py-4 text-gray-600">{{ formatNomor(item.phone) }}</td>
              <td class="px-6 py-4">
                <span
                  :class="item.aktif
                    ? 'bg-green-100 text-green-700 border-green-200'
                    : 'bg-gray-100 text-gray-500 border-gray-200'"
                  class="inline-block px-2.5 py-1 rounded-full text-xs font-bold border"
                >
                  {{ item.status === 'connected' ? 'Aktif' : 'Nonaktif' }}
                </span>
              </td>
              <td class="px-6 py-4 text-right whitespace-nowrap">
                <button
                  @click="openQrCode(item)"
                  class="text-indigo-600 hover:text-indigo-900 font-semibold text-sm mr-3"
                >
                  Show QR Code
                </button>
                <button
                  @click="openEdit(item)"
                  class="text-indigo-600 hover:text-indigo-900 font-semibold text-sm mr-3"
                >
                  Edit
                </button>
                <button
                  @click="confirmDelete(item)"
                  class="text-red-600 hover:text-red-900 font-semibold text-sm"
                >
                  Hapus
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- MODAL FORM (TAMBAH / EDIT) -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
      <div class="absolute inset-0 bg-black/40" @click="closeModal"></div>
      <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
          {{ isEditing ? 'Edit Nomor WhatsApp' : 'Tambah Nomor WhatsApp' }}
        </h3>

        <form @submit.prevent="submitForm" class="space-y-4">
          <div>
          <qrcode-vue :value="qr" level="H" render-as="svg" />
            <label class="block text-sm font-semibold text-gray-600 mb-1">Nama</label>
            <input
              v-model="form.nama"
              type="text"
              placeholder="Mis. Bendahara Sekolah"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-600 mb-1">Nomor WhatsApp</label>
            <input
              v-model="form.nomor"
              type="tel"
              placeholder="08xxxxxxxxxx"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-500"
            />
            <p class="text-[11px] text-gray-400 mt-1">Gunakan format 08xxxxxxxxxx</p>
          </div>

          <div class="flex items-center justify-between">
            <label class="text-sm font-semibold text-gray-600">Status</label>
            <div class="flex items-center gap-2">
              <span class="text-xs text-gray-500">{{ form.aktif ? 'Aktif' : 'Nonaktif' }}</span>
              <button
                type="button"
                @click="form.aktif = !form.aktif"
                :class="form.aktif ? 'bg-green-500' : 'bg-gray-300'"
                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
              >
                <span
                  :class="form.aktif ? 'translate-x-6' : 'translate-x-1'"
                  class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform"
                ></span>
              </button>
            </div>
          </div>

          <div class="flex justify-end gap-2 pt-2">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 transition"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold transition disabled:opacity-50"
            >
              {{ saving ? 'Menyimpan...' : 'Simpan' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL KONFIRMASI HAPUS -->
    <div
      v-if="showDelete"
      class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
      <div class="absolute inset-0 bg-black/40" @click="showDelete = false"></div>
      <div class="relative bg-white rounded-xl shadow-xl w-full max-w-sm p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-2">Hapus Nomor?</h3>
        <p class="text-sm text-gray-600 mb-4">
          Yakin ingin menghapus nomor <span class="font-semibold">{{ target?.nama }}</span>?
        </p>
        <div class="flex justify-end gap-2">
          <button
            @click="showDelete = false"
            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 transition"
          >
            Batal
          </button>
          <button
            @click="doDelete"
            :disabled="deleting"
            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold transition disabled:opacity-50"
          >
            {{ deleting ? 'Menghapus...' : 'Hapus' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
import { whatsappApi } from '@/services/whatsapp'
import QrcodeVue, { QrcodeCanvas, QrcodeSvg } from 'qrcode.vue'

const nomorList = ref([])
const search = ref('')
const loading = ref(false)
const saving = ref(false)
const deleting = ref(false)

const showModal = ref(false)
const showDelete = ref(false)
const isEditing = ref(false)
const form = ref({ nama: '', nomor: '', aktif: true })
const target = ref(null)
const qr = ref(null)

let searchTimer = null

const fetchData = async () => {
  loading.value = true
  try {
    const res = await whatsappApi.list({ search: search.value })
    nomorList.value = res.data.data.data
  } catch (err) {
    console.error('Gagal mengambil data WhatsApp:', err)
  } finally {
    loading.value = false
  }
}

const createSession = async () => {
  loading.value = true
  try {
    const res = await whatsappApi.createSession({name : 'satu'})
    // qr.value = res.data.data
    console.log(res.data)
  } catch (err) {
    console.error('Gagal mengambil data WhatsApp:', err)
  } finally {
    loading.value = false
  }
}

watch(search, () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(fetchData, 400)
})

const openCreate = async () => {
  isEditing.value = false
  form.value = { nama: '', nomor: '', aktif: true }
  await createSession()
  showModal.value = true
}

const openEdit = (item) => {
  isEditing.value = true
  form.value = { nama: item.nama, nomor: item.nomor, aktif: item.aktif }
  target.value = item
  showModal.value = true
}

const openQrCode = async (item) => {
  isEditing.value = true
  // Panggil getQr dan tunggu hasilnya
  const res = await getQr(item.id)
  
  if (res) {
    qr.value = res.data.data
    console.log(res.data)
  }
  
  showModal.value = true
}

const getQr = async (id) => {
  loading.value = true
  try {
    const response = await whatsappApi.getQr(id)
    return response // Kembalikan respons agar bisa diakses oleh openQrCode
  } catch (err) {
    console.error('Gagal mengambil data WhatsApp:', err)
    return null
  } finally {
    loading.value = false
  }
}


const closeModal = () => {
  showModal.value = false
  target.value = null
}

const submitForm = async () => {
  if (saving.value) return

  saving.value = true
  try {
    if (isEditing.value) {
      await whatsappApi.update(target.value.id, form.value)
    } else {
      await whatsappApi.store(form.value)
    }
    closeModal()
    await fetchData()
  } catch (err) {
    console.error('Gagal menyimpan data:', err)
  } finally {
    saving.value = false
  }
}

const confirmDelete = (item) => {
  target.value = item
  showDelete.value = true
}

const doDelete = async () => {
  if (deleting.value || !target.value) return

  deleting.value = true
  try {
    await whatsappApi.destroy(target.value.id)
    showDelete.value = false
    target.value = null
    await fetchData()
  } catch (err) {
    console.error('Gagal menghapus data:', err)
  } finally {
    deleting.value = false
  }
}

const formatNomor = (nomor) => {
  const cleaned = String(nomor || '').replace(/\D/g, '')
  if (cleaned.length >= 4) {
    return `${cleaned.slice(0, cleaned.length - 4)}-${cleaned.slice(-4)}`
  }
  return cleaned
}

onMounted(fetchData)
onBeforeUnmount(() => clearTimeout(searchTimer))
</script>
