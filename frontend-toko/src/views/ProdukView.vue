<script setup>
import { ref, onMounted } from 'vue'
import PublicLayout from '../components/PublicLayout.vue'
import api from '../utils/api'

const produks = ref([])
const loading = ref(true)
const searchQuery = ref('')

const fetchProduk = async () => {
  loading.value = true
  try {
    // Jika ada searchQuery, gunakan endpoint search, jika tidak ambil semua
    const url = searchQuery.value
      ? `/public/search?keyword=${searchQuery.value}`
      : `/public/produk`
    const response = await api.get(url)
    // ⚠️ Perhatikan: .data.data.data (karena API pakai paginate)
    produks.value = response.data.data.data || []
  } catch (error) {
    console.error("Gagal mengambil data produk", error)
  } finally {
    loading.value = false
  }
}

const handleSearch = () => {
  fetchProduk()
}

onMounted(() => {
  fetchProduk()
})
</script>

<template>
  <PublicLayout>
    <div class="container fade-in">
      <div class="d-flex justify-between align-center" style="margin-bottom: 2rem;">
        <h1 style="font-size: 2rem; margin: 0;">Katalog Produk</h1>

        <form @submit.prevent="handleSearch" class="search-bar d-flex gap-2">
          <input type="text" v-model="searchQuery" class="form-control" placeholder="Cari produk..." style="width: 250px;">
          <button type="submit" class="btn btn-primary">Cari</button>
        </form>
      </div>

      <div v-if="loading" class="text-center text-muted" style="padding: 4rem;">
        <div class="spinner"></div>
        <p style="margin-top: 1rem;">Memuat produk...</p>
      </div>

      <div v-else-if="produks.length === 0" class="text-center text-muted glass-card" style="padding: 4rem;">
        <span style="font-size: 3rem; display: block; margin-bottom: 1rem;">📭</span>
        <h3>Produk tidak ditemukan</h3>
        <p>Coba kata kunci lain.</p>
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
    </div>
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

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid rgba(99, 102, 241, 0.2);
  border-left-color: var(--primary-color);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>