<script setup>
import AdminLayout from '../components/AdminLayout.vue'
import { ref, onMounted } from 'vue'
import api from '../utils/api'

const stats = ref({
  produk: 0,
  kategori: 0,
  pelanggan: 0,
  pesanan: 0
})

const loading = ref(true)

onMounted(async () => {
  try {
    // Panggil 4 API sekaligus menggunakan Promise.all (lebih cepat!)
    const [resProduk, resKategori, resPelanggan, resPesanan] = await Promise.all([
      api.get('/produk'),
      api.get('/kategori'),
      api.get('/pelanggan'),
      api.get('/pesanan')
    ])

    stats.value = {
      produk: resProduk.data.data?.length || 0,
      kategori: resKategori.data.data?.length || 0,
      pelanggan: resPelanggan.data.data?.length || 0,
      pesanan: resPesanan.data.data?.length || 0
    }
  } catch (error) {
    console.error("Gagal memuat statistik", error)
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <AdminLayout>
    <div class="fade-in">
      <h2 style="margin-bottom: 1.5rem;">Dashboard</h2>

      <div v-if="loading" class="text-muted">Memuat data...</div>

      <div v-else class="grid grid-cols-4">
        <div class="stat-card glass-card">
          <div class="stat-icon" style="background: rgba(99, 102, 241, 0.1); color: var(--primary-color);">📦</div>
          <div class="stat-info">
            <h3 class="stat-value">{{ stats.produk }}</h3>
            <p class="stat-label text-muted">Total Produk</p>
          </div>
        </div>

        <div class="stat-card glass-card">
          <div class="stat-icon" style="background: rgba(236, 72, 153, 0.1); color: var(--secondary-color);">📁</div>
          <div class="stat-info">
            <h3 class="stat-value">{{ stats.kategori }}</h3>
            <p class="stat-label text-muted">Total Kategori</p>
          </div>
        </div>

        <div class="stat-card glass-card">
          <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">👥</div>
          <div class="stat-info">
            <h3 class="stat-value">{{ stats.pelanggan }}</h3>
            <p class="stat-label text-muted">Total Pelanggan</p>
          </div>
        </div>

        <div class="stat-card glass-card">
          <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">🛒</div>
          <div class="stat-info">
            <h3 class="stat-value">{{ stats.pesanan }}</h3>
            <p class="stat-label text-muted">Total Pesanan</p>
          </div>
        </div>
      </div>

      <div class="glass-card mt-4" style="margin-top: 2rem;">
        <h3>Selamat Datang di Admin Panel</h3>
        <p class="text-muted">Gunakan menu di sidebar untuk mengelola data toko Anda.</p>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
.stat-card {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  padding: 1.5rem;
}

.stat-icon {
  width: 60px;
  height: 60px;
  border-radius: var(--radius-full);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.75rem;
}

.stat-value {
  font-size: 1.75rem;
  margin: 0;
  line-height: 1.2;
}

.stat-label {
  margin: 0;
  font-size: 0.875rem;
}
</style>