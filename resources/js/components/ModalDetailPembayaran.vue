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
        <div class="font-medium text-gray-800">#{{pembayaran.id}}</div>

        <div class="text-gray-500">Tanggal</div>
        <div class="font-medium text-gray-800">{{ formatTanggal(pembayaran.tanggal_bayar) }}</div>

        <div class="text-gray-500">Nama Siswa</div>
        <div class="font-medium text-gray-800">{{pembayaran.siswa.nama}}</div>

        <div class="text-gray-500">Kelas</div>
        <div class="font-medium text-gray-800">{{ pembayaran.siswa.kelas }}</div>

        <div class="text-gray-500">Metode</div>
        <div class="font-medium text-gray-800">Tunai</div>
      </div>

      <hr>

      <!-- RINCIAN -->
      <div>
        <h3 class="font-semibold text-gray-700 mb-2">
          Rincian Pembayaran
        </h3>
        <div class="space-y-2">
          <div v-for="item in data" class="flex justify-between">
            <span>{{ item.item }}</span>
            <span class="font-medium">{{ formatUang(item.jumlah_bayar) }}</span>
          </div>
        </div>
      </div>

      <hr>

      <!-- TOTAL -->
      <div class="flex justify-between text-base font-semibold">
        <span>Total</span>
        <span>{{ formatUang(pembayaran.total_bayar) }}</span>
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
  pembayaran: Object,
  data: Object
})

const emit = defineEmits(['close'])

const handleEsc = (e) => {
  if (e.key === 'Escape') {
    emit('close')
  }
}

const formatUang = (value) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0
  }).format(value)
}

const formatTanggal = (tgl) => {
  return new Date(tgl).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'long',
    year: 'numeric'
  });
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
