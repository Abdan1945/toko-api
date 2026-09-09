<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import PublicLayout from '../components/PublicLayout.vue'

const router = useRouter()
const cartItems = ref([])

// 1. Ambil data keranjang dari localStorage saat halaman dimuat
const loadCart = () => {
  const savedCart = localStorage.getItem('cart')
  cartItems.value = savedCart ? JSON.parse(savedCart) : []
}

onMounted(() => {
  loadCart()
})

// 2. Simpan perubahan keranjang ke localStorage
const saveCart = () => {
  localStorage.setItem('cart', JSON.stringify(cartItems.value))
}

// 3. Tambah / Kurangi Jumlah Produk
const updateQuantity = (index, change) => {
  const newQty = cartItems.value[index].jumlah + change
  if (newQty > 0) {
    cartItems.value[index].jumlah = newQty
    saveCart()
  }
}

// 4. Hapus Item dari Keranjang
const removeItem = (index) => {
  if (confirm('Apakah Anda yakin ingin menghapus produk ini dari keranjang?')) {
    cartItems.value.splice(index, 1)
    saveCart()
  }
}

// 5. Kosongkan Keranjang
const clearCart = () => {
  if (confirm('Kosongkan semua barang di keranjang?')) {
    cartItems.value = []
    localStorage.removeItem('cart')
  }
}

// 6. Hitung Total Harga Keseluruhan (Computed Property)
const totalHarga = computed(() => {
  return cartItems.value.reduce((total, item) => {
    return total + (Number(item.harga_barang) * item.jumlah)
  }, 0)
})

// 7. Format Rupiah
const formatRupiah = (angka) => {
  return 'Rp ' + Number(angka || 0).toLocaleString('id-ID')
}

// 8. Checkout / Lanjut ke Pembelian
const handleCheckout = () => {
  alert('Terima kasih! Pesanan Anda sedang diproses.')
  // Kosongkan keranjang setelah checkout
  cartItems.value = []
  localStorage.removeItem('cart')
  router.push('/produk')
}
</script>

<template>
  <PublicLayout>
    <div class="fade-in container" style="margin-top: 2rem; margin-bottom: 4rem;">
      <h2 style="margin-bottom: 1.5rem;">🛒 Keranjang Belanja</h2>

      <!-- Tampilan Jika Keranjang Kosong -->
      <div v-if="cartItems.length === 0" class="glass-card text-center" style="padding: 3rem;">
        <span style="font-size: 4rem; display: block; margin-bottom: 1rem;">🛒</span>
        <h3>Keranjang Belanja Anda Kosong</h3>
        <p class="text-muted" style="margin-bottom: 1.5rem;">
          Anda belum menambahkan produk apa pun ke keranjang.
        </p>
        <router-link to="/produk" class="btn btn-primary">
          Mulai Belanja &rarr;
        </router-link>
      </div>

      <!-- Tampilan Jika Keranjang Berisi Produk -->
      <div v-else class="grid grid-cols-3 gap-4" style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        
        <!-- List Item Keranjang (Kiri) -->
        <div class="glass-card">
          <div class="d-flex justify-between align-center mb-4" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
            <h3>Daftar Produk ({{ cartItems.length }})</h3>
            <button @click="clearCart" class="btn btn-danger" style="padding: 0.35rem 0.75rem; font-size: 0.85rem;">
              Kosongkan Keranjang
            </button>
          </div>

          <div class="table-responsive">
            <table>
              <thead>
                <tr>
                  <th>No</th>
                  <th>Produk</th>
                  <th style="text-align: right;">Harga</th>
                  <th style="text-align: center;">Jumlah</th>
                  <th style="text-align: right;">Subtotal</th>
                  <th style="text-align: center;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in cartItems" :key="item.id">
                  <td>{{ index + 1 }}</td>
                  <td><strong>{{ item.nama_barang }}</strong></td>
                  <td style="text-align: right;">{{ formatRupiah(item.harga_barang) }}</td>
                  
                  <!-- Pengatur Jumlah Item -->
                  <td style="text-align: center;">
                    <div class="d-flex align-center justify-center gap-1">
                      <button @click="updateQuantity(index, -1)" class="btn-qty">-</button>
                      <span style="padding: 0 0.5rem; font-weight: 600;">{{ item.jumlah }}</span>
                      <button @click="updateQuantity(index, 1)" class="btn-qty">+</button>
                    </div>
                  </td>

                  <td style="text-align: right; font-weight: 600;">
                    {{ formatRupiah(item.harga_barang * item.jumlah) }}
                  </td>

                  <td style="text-align: center;">
                    <button @click="removeItem(index)" class="btn btn-danger" style="padding: 0.25rem 0.5rem;" title="Hapus">
                      🗑️
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Ringkasan Belanja (Kanan) -->
        <div class="glass-card" style="height: fit-content;">
          <h3 style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
            Ringkasan Belanja
          </h3>

          <div class="d-flex justify-between align-center" style="margin-bottom: 1rem;">
            <span class="text-muted">Total Items:</span>
            <strong>{{ cartItems.reduce((sum, item) => sum + item.jumlah, 0) }} pcs</strong>
          </div>

          <hr style="border: 1px dashed var(--border-color); margin: 1rem 0;">

          <div class="d-flex justify-between align-center" style="margin-bottom: 1.5rem;">
            <span style="font-size: 1.1rem; font-weight: 600;">Total Harga:</span>
            <span style="font-size: 1.4rem; font-weight: 700; color: var(--primary-color);">
              {{ formatRupiah(totalHarga) }}
            </span>
          </div>

          <button @click="handleCheckout" class="btn btn-primary w-100" style="padding: 0.75rem 1rem; font-size: 1rem;">
            Proses Checkout &rarr;
          </button>
        </div>

      </div>
    </div>
  </PublicLayout>
</template>

<style scoped>
.btn-qty {
  background: var(--bg-light);
  border: 1px solid var(--border-color);
  color: var(--text-color);
  width: 28px;
  height: 28px;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
}

.btn-qty:hover {
  background: var(--border-color);
}

.w-100 {
  width: 100%;
}

.gap-1 {
  gap: 0.25rem;
}

.justify-center {
  justify-content: center;
}
</style>