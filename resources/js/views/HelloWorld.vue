<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="p-8 bg-white shadow-xl rounded-2xl">
      <h1 class="text-4xl font-extrabold text-blue-600 animate-bounce">
        Hello World!
      </h1>
      <p class="mt-4 text-gray-600">
        Berhasil terintegrasi dengan <span class="font-bold text-teal-500">Vue 3</span> dan <span class="font-bold text-indigo-500">Tailwind CSS</span>.
      </p>
      <router-link to="/dashboard" class="inline-block mt-6 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
        Ke Dashboard
      </router-link>
    </div>
  </div>
</template>