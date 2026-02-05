<template>
  <input
    type="text"
    :value="displayValue"
    @input="onInput"
    @focus="onFocus"
    class="text-right w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none font-bold text-slate-700 text-sm"
  />
</template>

<script>
{/* <script setup> */}
// import { computed } from 'vue';

// const props = defineProps({
//     modelValue: {
//         type: Number,
//         default: 0
//     },
//     max: {
//     type: Number,
//     default: null
//     }
// });

// const emit = defineEmits(['update:modelValue']);

// const displayValue = computed(() => {
//     return new Intl.NumberFormat('id-ID', {
//         style: 'currency',
//         currency: 'IDR',
//         minimumFractionDigits: 0
//     }).format(props.modelValue || 0);
// });

// const onInput = (event) => {
//     const raw = event.target.value.replace(/[^\d]/g, '');
//     let value = raw ? parseInt(raw) : 0;

//     if (props.max !== null) {
//         value = Math.min(value, props.max);
//     }

//     emit('update:modelValue', value);
// }

export default {
  name: 'CurrencyInput',

  props: {
    modelValue: {
      type: Number,
      default: 0
    },
    max: {
      type: Number,
      default: null
    }
  },

  emits: ['update:modelValue'],

  computed: {
    displayValue() {
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
      }).format(this.modelValue || 0)
    }
  },

  methods: {
    onInput(event) {
      const raw = event.target.value.replace(/[^\d]/g, '')
      let value = raw ? parseInt(raw) : 0

      if (this.max !== null) {
        value = Math.min(value, this.max)
      }

      this.$emit('update:modelValue', value)
    },

    onFocus(event) {
      // Optional: select all text saat focus
      event.target.select()
    }
  }
}
</script>
