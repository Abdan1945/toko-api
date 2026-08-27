<script setup>
import { ref, onMounted } from 'vue'
import PublicLayout from '../components/PublicLayout.vue'
import api from '../utils/api'

const produks = ref([])
const loading = ref(true)

onMounted(async () => {
  try {
    const response = await api.get('/public/produk')
    // ⚠️ API pakai paginate() → data ada di response.data.data.data (3 level!)
    produks.value = response.data.data.data.slice(0, 4) // Ambil 4 produk terbaru
  } catch (error) {
    console.error("Gagal mengambil data produk", error)
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <PublicLayout>
    <!-- Hero Section -->
    <section class="hero glass-card fade-in" style="margin-bottom: 4rem; text-align: center; padding: 4rem 2rem;">
      <h1 style="font-size: 3rem; margin-bottom: 1rem;">Selamat Datang di Toko Sederhana</h1>
      <p class="text-muted" style="font-size: 1.2rem; max-width: 600px; margin: 0 auto 2rem auto;">
        Temukan berbagai produk berkualitas dengan harga terbaik. Belanja mudah, cepat, dan aman hanya di Toko Sederhana.
      </p>
      <router-link to="/produk" class="btn btn-primary" style="font-size: 1.1rem; padding: 0.75rem 2rem;">
        Mulai Belanja &rarr;
      </router-link>
    </section>

    <!-- Featured Products -->
    <section class="container fade-in" style="animation-delay: 0.2s;">
      <div class="d-flex justify-between align-center" style="margin-bottom: 2rem;">
        <h2>Produk Terbaru</h2>
        <router-link to="/produk" class="text-primary" style="font-weight: 500; color: var(--primary-color);">Lihat Semua</router-link>
      </div>

      <div v-if="loading" class="text-center text-muted" style="padding: 2rem;">
        Memuat produk...
      </div>

      <div v-else class="grid grid-cols-4">
        <div v-for="produk in produks" :key="produk.id" class="product-card glass-card">
          <div class="product-image">
            <span class="placeholder-img">📦</span>
          </div>
          <div class="product-info">
            <h3 class="product-title">{{ produk.nama_barang }}</h3>
            <p class="product-price">Rp {{ produk.harga_barang.toLocaleString('id-ID') }}</p>
            <router-link :to="`/detail-produk/${produk.id}`" class="btn btn-secondary w-100" style="margin-top: 1rem;">
              Detail
            </router-link>
          </div>
        </div>
      </div>
    </section>
  </PublicLayout>
</template>

<style scoped>
.product-card {
  padding: 0;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.product-image {
  background: var(--bg-light);
  height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 4rem;
  border-bottom: 1px solid var(--border-color);
}

.product-info {
  padding: 1.5rem;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.product-title {
  font-size: 1.1rem;
  margin-bottom: 0.5rem;
  flex: 1;
}

.product-price {
  font-weight: 700;
  color: var(--primary-color);
  font-size: 1.2rem;
  margin: 0;
}

.w-100 {
  width: 100%;
}
</style>