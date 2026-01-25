<template>
  <div v-if="show" class="overlay">
    <div class="modal">
      <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">

        <div class="bg-white w-full max-w-3xl rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">

            <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold">{{ siswa.nama }}</h2>
            <p class="text-sm text-gray-500">
                Kelas X IPA 1 · TP 2024/2025
            </p>
            </div>

            <div class="px-6 py-3 bg-gray-50 border-b grid grid-cols-3 text-center text-sm">
            <div>
                <p class="text-gray-500">Total</p>
                <p class="font-semibold">Rp 5.000.000</p>
            </div>
            <div>
                <p class="text-gray-500">Dibayar</p>
                <p class="font-semibold text-green-600">Rp 3.500.000</p>
            </div>
            <div>
                <p class="text-gray-500">Sisa</p>
                <p class="font-semibold text-red-600">Rp 1.500.000</p>
            </div>
            </div>

            <div class="border-b">
            <nav class="flex">
                <button class="px-4 py-2 text-sm border-b-2 border-blue-600 text-blue-600">
                SPP <span class="ml-1 text-xs bg-red-100 text-red-600 px-2 rounded-full">2</span>
                </button>
                <button class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">
                Buku
                </button>
                <button class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">
                Ekstrakurikuler
                </button>
                <button class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">
                Pembangunan
                </button>
            </nav>
            </div>

            <!-- TAB CONTENT -->
            <div class="px-6 py-4 max-h-[50vh] overflow-y-auto">

            <!-- SPP TAB -->
            <table class="w-full text-sm border rounded">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left">Bulan</th>
                    <th class="px-3 py-2 text-right">Nominal</th>
                    <th class="px-3 py-2 text-right">Dibayar</th>
                    <th class="px-3 py-2 text-right">Sisa</th>
                    <th class="px-3 py-2 text-center">Status</th>
                </tr>
                </thead>
                <tbody class="divide-y">
                <tr>
                    <td class="px-3 py-2">Jan 2025</td>
                    <td class="px-3 py-2 text-right">500.000</td>
                    <td class="px-3 py-2 text-right">500.000</td>
                    <td class="px-3 py-2 text-right">0</td>
                    <td class="px-3 py-2 text-center text-green-600">LUNAS</td>
                </tr>
                <tr>
                    <td class="px-3 py-2">Feb 2025</td>
                    <td class="px-3 py-2 text-right">500.000</td>
                    <td class="px-3 py-2 text-right">300.000</td>
                    <td class="px-3 py-2 text-right">200.000</td>
                    <td class="px-3 py-2 text-center text-yellow-600">SEBAGIAN</td>
                </tr>
                <tr>
                    <td class="px-3 py-2">Mar 2025</td>
                    <td class="px-3 py-2 text-right">500.000</td>
                    <td class="px-3 py-2 text-right">0</td>
                    <td class="px-3 py-2 text-right">500.000</td>
                    <td class="px-3 py-2 text-center text-red-600">BELUM</td>
                </tr>
                </tbody>
            </table>

            </div>

            <!-- FOOTER -->
            <div class="px-6 py-4 border-t flex justify-end gap-3 bg-gray-50">
            <button class="px-4 py-2 text-sm border rounded">
                Tutup
            </button>
            <button class="px-4 py-2 text-sm bg-blue-600 text-white rounded">
                Bayar
            </button>
            </div>

        </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, onUnmounted } from 'vue'

defineProps({
  show: Boolean,
  siswa: Object
})

const emit = defineEmits(['close'])

const handleEsc = (e) => {
  if (e.key === 'Escape') {
    emit('close')
  }
}

onMounted(() => {
  window.addEventListener('keydown', handleEsc)
})

onUnmounted(() => {
  window.removeEventListener('keydown', handleEsc)
})

</script>

<style scoped>
.overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.4);
  display: flex;
  align-items: center;
  justify-content: center;
}
.modal {
  background: white;
  padding: 20px;
}
</style>
