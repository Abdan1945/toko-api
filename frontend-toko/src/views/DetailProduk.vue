<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import PublicLayout from '../components/PublicLayout.vue'
import api from '../utils/api'

const route = useRoute()
const produkId = route.params.id  // Ambil ID dari URL (/detail-produk/3 → id = 3)

const produk = ref(null)
const loading = ref(true)
const errorMsg = ref('')

const fetchDetail = async () => {
  try {
    const response = await api.get(`/public/produk/${produkId}`)
    produk.value = response.data.data
  } catch (error) {
    errorMsg.value = error.response?.status === 404
      ? 'Produk tidak ditemukan.'
      : 'Gagal memuat detail produk.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchDetail()
})
</script>

<template>
  <PublicLayout>
    <div class="container fade-in" style="padding-top: 2rem;">
      <router-link to="/produk" class="text-muted d-inline-block" style="margin-bottom: 2rem;">
        &larr; Kembali ke Katalog
      </router-link>

      <div v-if="loading" class="text-center text-muted" style="padding: 4rem;">
        <div class="spinner"></div>
        <p style="margin-top: 1rem;">Memuat detail produk...</p>
      </div>

      <div v-else-if="errorMsg" class="alert alert-danger">
        {{ errorMsg }}
      </div>

      <div v-else-if="produk" class="grid" style="grid-template-columns: 1fr 1fr; gap: 3rem;">
        <div class="product-image-large glass-card">
          <span class="placeholder-img" style="font-size: 6rem;">📦</span>
        </div>

        <div class="product-details">
          <h1 class="product-title">{{ produk.nama_barang }}</h1>

          <div class="product-meta d-flex gap-4 text-muted" style="margin-bottom: 1.5rem;">
            <span><span class="icon">📁</span> {{ produk.kategori?.nama_kategori || 'Kategori' }}</span>
            <span><span class="icon">📦</span> Stok: {{ produk.stok }}</span>
          </div>

          <h2 class="product-price">Rp {{ produk.harga_barang.toLocaleString('id-ID') }}</h2>

          <div class="product-description" style="margin: 2rem 0;">
            <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem;">Deskripsi Produk</h3>
            <p style="white-space: pre-line; color: var(--text-dark);">
              {{ produk.deskripsi || 'Tidak ada deskripsi.' }}
            </p>
          </div>

          <button class="btn btn-primary w-100" style="padding: 1rem; font-size: 1.1rem;" :disabled="produk.stok === 0">
            {{ produk.stok === 0 ? 'Stok Habis' : 'Tambah ke Keranjang' }}
          </button>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<style scoped>
.product-image-large {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 400px;
  background: var(--bg-surface);
}

.product-title {
  font-size: 2.5rem;
  margin-bottom: 1rem;
  background: none;
  -webkit-text-fill-color: var(--text-dark);
}

.product-price {
  font-size: 2rem;
  color: var(--primary-color);
  font-weight: 700;
}

.alert-danger {
  background-color: rgba(239, 68, 68, 0.1);
  color: #ef4444;
  padding: 1rem;
  border-radius: var(--radius-md);
  border: 1px solid rgba(239, 68, 68, 0.2);
}

.w-100 { width: 100%; }
.d-inline-block { display: inline-block; }

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

@media (max-width: 768px) {
  .grid {
    grid-template-columns: 1fr !important;
  }
}
</style>