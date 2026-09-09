<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import PublicLayout from '../components/PublicLayout.vue'
import api from '../utils/api'

const route = useRoute()

const produk = ref(null)
const loading = ref(true)
const showNotification = ref(false)
let toastTimeout = null

// Fetch detail produk dari API berdasarkan ID di URL
const fetchDetailProduk = async () => {
  loading.value = true
  try {
    const response = await api.get(`/produk/${route.params.id}`)
    produk.value = response.data.data || response.data
  } catch (error) {
    console.error('Gagal mengambil detail produk:', error)
  } finally {
    loading.value = false
  }
}

// Fungsi Tambah ke Keranjang dengan Notifikasi Bagus
const addToCart = (item) => {
  if (!item) return

  // 1. Ambil data keranjang saat ini dari localStorage
  const savedCart = localStorage.getItem('cart')
  const cart = savedCart ? JSON.parse(savedCart) : []

  // 2. Cek apakah produk sudah ada di keranjang
  const existingIndex = cart.findIndex((cartItem) => cartItem.id === item.id)

  if (existingIndex !== -1) {
    cart[existingIndex].jumlah += 1
  } else {
    cart.push({
      id: item.id,
      nama_barang: item.nama_barang,
      harga_barang: item.harga_barang,
      jumlah: 1
    })
  }

  // 3. Simpan perubahan ke localStorage
  localStorage.setItem('cart', JSON.stringify(cart))

  // 4. Tampilkan Notifikasi Toast Modern
  showNotification.value = true
  if (toastTimeout) clearTimeout(toastTimeout)
  toastTimeout = setTimeout(() => {
    showNotification.value = false
  }, 4000)
}

onMounted(() => {
  fetchDetailProduk()
})
</script>

<template>
  <PublicLayout>
    <!-- Modern Toast Notification -->
    <Transition name="toast">
      <div v-if="showNotification" class="toast-card d-flex align-center justify-between gap-4">
        <div class="d-flex align-center gap-3">
          <div class="toast-icon">✓</div>
          <div>
            <h4 class="toast-title">Berhasil Ditambahkan!</h4>
            <p class="toast-desc">Produk telah masuk ke keranjang belanja Anda.</p>
          </div>
        </div>
        <div class="d-flex align-center gap-2">
          <router-link to="/keranjang" class="toast-btn">
            Lihat Keranjang
          </router-link>
          <button @click="showNotification = false" class="toast-close" title="Tutup">&times;</button>
        </div>
      </div>
    </Transition>

    <div class="container fade-in" style="padding-top: 2rem;">
      <!-- Loading State -->
      <div v-if="loading" style="text-align: center; padding: 3rem;">
        Memuat detail produk...
      </div>

      <!-- Detail Produk -->
      <div v-else-if="produk" class="grid" style="grid-template-columns: 1fr 1fr; gap: 3rem;">
        
        <!-- Gambar Produk -->
        <div class="product-image-large glass-card">
          <span style="font-size: 5rem;">📦</span>
        </div>

        <!-- Info Produk -->
        <div class="product-details">
          <h1 class="product-title">{{ produk.nama_barang }}</h1>

          <div class="product-meta d-flex gap-4 text-muted" style="margin-bottom: 1.5rem;">
            <span><span class="icon">🏷️</span> {{ produk.kategori?.nama_kategori || 'Kategori' }}</span>
            <span><span class="icon">📦</span> Stok: {{ produk.stok }}</span>
          </div>

          <h2 class="product-price">
            Rp {{ Number(produk.harga_barang || 0).toLocaleString('id-ID') }}
          </h2>

          <div class="product-description" style="margin: 2rem 0;">
            <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem;">Deskripsi Produk</h3>
            <p style="white-space: pre-line; color: var(--text-dark);">
              {{ produk.deskripsi || 'Tidak ada deskripsi.' }}
            </p>
          </div>

          <!-- Tombol Tambah ke Keranjang -->
          <button 
            class="btn btn-primary w-100" 
            style="padding: 1rem; font-size: 1.1rem;" 
            :disabled="produk.stok === 0"
            @click="addToCart(produk)"
          >
            {{ produk.stok === 0 ? 'Stok Habis' : 'Tambah ke Keranjang' }}
          </button>
        </div>

      </div>

      <!-- Jika Produk Tidak Ditemukan -->
      <div v-else class="text-center" style="padding: 3rem;">
        Produk tidak ditemukan.
      </div>
    </div>
  </PublicLayout>
</template>

<style scoped>
/* Styling Modern Toast */
.toast-card {
  position: fixed;
  bottom: 30px;
  right: 30px;
  background: #ffffff;
  color: #1f2937;
  padding: 1rem 1.25rem;
  border-radius: 12px;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
  border-left: 5px solid #10b981;
  z-index: 9999;
  min-width: 320px;
  max-width: 450px;
}

.toast-icon {
  background: #ecfdf5;
  color: #10b981;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 0.9rem;
}

.toast-title {
  margin: 0;
  font-size: 0.95rem;
  font-weight: 600;
  color: #111827;
}

.toast-desc {
  margin: 0;
  font-size: 0.8rem;
  color: #6b7280;
}

.toast-btn {
  background: #10b981;
  color: #ffffff;
  padding: 0.4rem 0.8rem;
  border-radius: 6px;
  text-decoration: none;
  font-size: 0.8rem;
  font-weight: 600;
  white-space: nowrap;
  transition: background 0.2s;
}

.toast-btn:hover {
  background: #059669;
}

.toast-close {
  background: transparent;
  border: none;
  color: #9ca3af;
  font-size: 1.25rem;
  cursor: pointer;
  line-height: 1;
  padding: 0 0.2rem;
}

.toast-close:hover {
  color: #4b5563;
}

/* Animasi Transition Toast Vue */
.toast-enter-active,
.toast-leave-active {
  transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}

.toast-enter-from {
  opacity: 0;
  transform: translateY(20px) scale(0.95);
}

.toast-leave-to {
  opacity: 0;
  transform: translateY(20px) scale(0.95);
}

/* Helper Utilities */
.product-image-large {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 400px;
  background: var(--bg-surface);
  border-radius: 8px;
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
  font-weight: bold;
}

.w-100 {
  width: 100%;
}

.gap-2 { gap: 0.5rem; }
.gap-3 { gap: 0.75rem; }
.gap-4 { gap: 1rem; }
.d-flex { display: flex; }
.align-center { align-items: center; }
.justify-between { justify-content: space-between; }
</style>