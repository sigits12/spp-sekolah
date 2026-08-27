<template>
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
  <div class="sm:mx-auto sm:w-full sm:max-w-md">
    <div class="flex justify-center">
      <div class="h-16 w-16 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-200">
        <span class="text-white text-3xl font-bold">S</span>
      </div>
    </div>
    <h2 class="mt-6 text-center text-2xl font-extrabold text-gray-800">
      Sistem Informasi Sekolah
    </h2>
    <p class="mt-2 text-center text-sm text-gray-600">
      Silakan login untuk mengelola pembayaran
    </p>
  </div>

  <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
    <div class="bg-white py-8 px-6 shadow-sm border border-gray-200 rounded-xl sm:px-10">

      <div v-if="errorMessage" class="mb-4 p-3 rounded-md bg-red-50 border border-red-200 text-red-600 text-sm">
        {{ errorMessage }}
      </div>
      <form class="space-y-6" @submit.prevent="handleLogin">
        
        <div>
          <label for="email" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">
            Email / Username
          </label>
          <div class="mt-1">
            <input 
              v-model="loginForm.email"
              name="email" 
              type="text" 
              required 
              class="w-full px-3 py-2.5 rounded-md bg-gray-50 border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none text-sm transition-all"
              placeholder="Masukkan username Anda"
              :disabled="isLoading"
            >
          </div>
        </div>

        <div>
          <label for="password" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">
            Password
          </label>
          <div class="mt-1">
            <input 
              v-model="loginForm.password"
              name="password" 
              type="password" 
              required 
              class="w-full px-3 py-2.5 rounded-md bg-gray-50 border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none text-sm transition-all"
              placeholder="••••••••"
              :disabled="isLoading"
            >
          </div>
        </div>

        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <input 
              id="remember-me" 
              name="remember-me" 
              type="checkbox" 
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded cursor-pointer"
            >
            <label for="remember-me" class="ml-2 block text-sm text-gray-600 cursor-pointer">
              Ingat saya
            </label>
          </div>

          <div class="text-sm">
            <a href="#" class="font-medium text-blue-600 hover:text-blue-500">
              Lupa password?
            </a>
          </div>
        </div>

        <div>
          <button 
            type="submit" 
            :disabled="isLoading"
            class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all active:scale-[0.98]"
          >
          <span v-if="isLoading" class="flex items-center gap-2">
            <!-- Spinner Sederhana -->
            <svg class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Memproses...
          </span>
          <span v-else>Masuk ke Panel</span>
          </button>
        </div>
      </form>

      <div class="mt-6">
        <div class="relative">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200"></div>
          </div>
          <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white text-gray-500">
              Butuh bantuan?
            </span>
          </div>
        </div>
        <div class="mt-6 text-center text-sm">
          <p class="text-gray-600">Hubungi Admin IT Sekolah</p>
        </div>
      </div>
    </div>

    <p class="mt-8 text-center text-xs text-gray-400">
      &copy; 2026 Nama Sekolah Anda. All rights reserved.
    </p>
  </div>
</div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useAuthStore } from '@/stores/auth'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const auth = useAuthStore()

const roleRedirect = {
  admin: '/pembayaran',
  tu: '/tu/dashboard',
  wali_kelas: '/wali-kelas/dashboard',
  kepsek: '/kepsek/dashboard',
}


// State menggunakan reactive untuk objek form
const loginForm = reactive({
  email: '',
  password: ''
});

// State menggunakan ref untuk nilai primitif
const isLoading = ref(false);
const errorMessage = ref(null);

const handleLogin = async () => {
  isLoading.value = true;
  errorMessage.value = null;

  try {
    const response = await auth.login({
      email: loginForm.email,
      password: loginForm.password
    });

    router.push(roleRedirect[auth.user.role] || '/')

    // const token = response.data.token;
    // localStorage.setItem('auth_token', token);
    
    // window.location.href = '/pembayaran'; 

  } catch (error) {
    if (error.response && error.response.status === 401) {
      errorMessage.value = 'Email atau password salah.';
    } else {
      errorMessage.value = 'Terjadi kesalahan pada server. Silakan coba lagi.';
    }
  } finally {
    isLoading.value = false;
  }
};
</script>