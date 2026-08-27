<script setup>
import { RouterView } from 'vue-router'
</script>

<template>
  <RouterView v-slot="{ Component }">
    <!-- Efek transisi halaman fade saat berpindah route -->
    <transition name="page" mode="out-in">
      <component :is="Component" />
    </transition>
  </RouterView>
</template>

<style scoped>
/* Transisi halaman sudah ada di style.css */
</style>
