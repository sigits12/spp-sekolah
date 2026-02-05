<template>
<!-- BACKDROP -->
<div class="fixed inset-0 bg-black/50 flex items-center justify-center p-3 z-[9999]">
    <!-- MODAL -->
    <div class="bg-white w-full max-w-md rounded-lg shadow-lg overflow-hidden relative z-[10000] flex flex-col max-h-[90vh]">
    <!-- HEADER -->
    <div class="px-4 py-3 border-b flex items-center justify-between sticky top-0 bg-white z-10">
      <h2 class="text-base font-semibold text-gray-800">
        Detail Pembayaran
      </h2>
      <button class="text-gray-400 hover:text-red-500 text-xl leading-none" @click="emit('close')">
        &times;
      </button>
    </div>

    <!-- BODY -->
    <div class="p-4 space-y-3 text-sm overflow-y-auto">
      
      <div class="grid grid-cols-2 gap-y-2">
        <div class="text-gray-500">ID Transaksi</div>
        <div class="font-medium text-gray-800">#TRX-2026-0012</div>

        <div class="text-gray-500">Tanggal</div>
        <div class="font-medium text-gray-800">05 Feb 2026</div>

        <div class="text-gray-500">Nama Siswa</div>
        <div class="font-medium text-gray-800">Ahmad Fauzi</div>

        <div class="text-gray-500">Kelas</div>
        <div class="font-medium text-gray-800">3A</div>

        <div class="text-gray-500">Metode</div>
        <div class="font-medium text-gray-800">Tunai</div>

        <div class="text-gray-500">Status</div>
        <div>
          <span class="inline-block px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-xs font-medium">
            Lunas
          </span>
        </div>
      </div>

      <hr>

      <!-- RINCIAN -->
      <div>
        <h3 class="font-semibold text-gray-700 mb-2">
          Rincian Pembayaran
        </h3>

        <div class="space-y-2">
          <div class="flex justify-between">
            <span>SPP - Feb 2026</span>
            <span class="font-medium">Rp 25.000</span>
          </div>
          <div class="flex justify-between">
            <span>Komite</span>
            <span class="font-medium">Rp 50.000</span>
          </div>
        </div>
      </div>

      <hr>

      <!-- TOTAL -->
      <div class="flex justify-between text-base font-semibold">
        <span>Total</span>
        <span>Rp 75.000</span>
      </div>

    </div>

    <!-- FOOTER -->
    <div class="px-4 py-3 border-t bg-gray-50 flex justify-end sticky bottom-0">
      <button class="px-4 py-2 text-sm rounded-md border hover:bg-gray-100" @click="emit('close')">
        Tutup
      </button>
    </div>

  </div>
</div>

</template>

<script setup>
import { ref, onMounted, onUnmounted, computed, watch } from 'vue'

const props = defineProps({
  loading: Boolean,
  siswa: Object,
  data: Object
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
</style>
