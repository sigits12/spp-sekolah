<template>
  <div class="bg-white p-4 rounded shadow">
    <h2 class="font-semibold mb-4">{{ title }}</h2>

    <p
      v-if="!items.length"
      class="text-sm text-gray-500"
    >
      Tidak ada pembayaran
    </p>

    <table
      v-else
      class="w-full text-sm"
    >
      <thead class="bg-gray-50">
        <tr>
          <th class="text-left px-3 py-2">Item</th>
          <th class="text-right px-3 py-2">Tagihan</th>
          <th class="text-right px-3 py-2">Dibayar</th>
          <th class="text-right px-3 py-2">Sisa</th>
        </tr>
      </thead>

      <tbody class="divide-y">
        <tr
          v-for="(row, i) in items"
          :key="i"
        >
          <td class="px-3 py-2">{{ row.item }}</td>
          <td class="px-3 py-2 text-right">
            {{ rupiah(row.total_tagihan) }}
          </td>
          <td class="px-3 py-2 text-right">
            {{ rupiah(row.total_dibayar) }}
          </td>
          <td class="px-3 py-2 text-right">
            {{ rupiah(row.sisa) }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  items: {
    type: Array,
    default: () => []
  }
})

const rupiah = (n) =>
  Number(n || 0).toLocaleString('id-ID')
</script>
