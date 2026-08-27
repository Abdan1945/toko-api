<script setup>
import { ref, onMounted } from 'vue'
import AdminLayout from '../components/AdminLayout.vue'
import api from '../utils/api'

const produks = ref([])
const kategoris = ref([])   // Untuk pilihan dropdown di form
const loading = ref(true)

// State Form
const showForm = ref(false)
const isEdit = ref(false)
const form = ref({
  id: null,
  nama_barang: '',
  harga_barang: 0,
  stok: 0,
  deskripsi: '',
  id_kategori: ''
})

// Ambil daftar kategori (untuk dropdown)
const fetchKategoris = async () => {
  try {
    const response = await api.get('/kategori')
    kategoris.value = response.data.data
  } catch (error) {
    console.error("Gagal mengambil kategori", error)
  }
}

// Ambil daftar produk
const fetchProduk = async () => {
  loading.value = true
  try {
    const response = await api.get('/produk')
    produks.value = response.data.data
  } catch (error) {
    console.error("Gagal mengambil produk", error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchKategoris()
  fetchProduk()
})

// Buka form Tambah
const openAddForm = () => {
  isEdit.value = false
  form.value = { id: null, nama_barang: '', harga_barang: 0, stok: 0, deskripsi: '', id_kategori: kategoris.value[0]?.id || '' }
  showForm.value = true
}

// Buka form Edit (isi dengan data produk yang diklik)
const openEditForm = (produk) => {
  isEdit.value = true
  form.value = { ...produk }   // Salin semua data produk ke form
  showForm.value = true
}

// Simpan (POST = tambah, PUT = edit)
const saveProduk = async () => {
  try {
    if (isEdit.value) {
      await api.put(`/produk/${form.value.id}`, form.value)
    } else {
      await api.post('/produk', form.value)
    }
    showForm.value = false
    fetchProduk()   // Refresh data!
  } catch (error) {
    alert("Gagal menyimpan produk: " + (error.response?.data?.message || error.message))
  }
}

// Hapus produk
const hapusProduk = async (id) => {
  if (confirm('Yakin ingin menghapus produk ini?')) {
    try {
      await api.delete(`/produk/${id}`)
      fetchProduk()   // Refresh data!
    } catch (error) {
      alert("Gagal menghapus produk")
    }
  }
}
</script>

<template>
  <AdminLayout>
    <div class="fade-in">
      <div class="d-flex justify-between align-center" style="margin-bottom: 1.5rem;">
        <h2>Manajemen Produk</h2>
        <button v-if="!showForm" @click="openAddForm" class="btn btn-primary">+ Tambah Produk</button>
      </div>

      <!-- Form Tambah / Edit -->
      <div v-if="showForm" class="glass-card mb-4" style="margin-bottom: 2rem;">
        <h3>{{ isEdit ? 'Edit Produk' : 'Tambah Produk Baru' }}</h3>
        <form @submit.prevent="saveProduk" style="margin-top: 1.5rem;">
          <div class="grid grid-cols-2">
            <div class="form-group">
              <label class="form-label">Nama Barang</label>
              <input type="text" v-model="form.nama_barang" class="form-control" required>
            </div>

            <div class="form-group">
              <label class="form-label">Kategori</label>
              <select v-model="form.id_kategori" class="form-control" required>
                <option v-for="k in kategoris" :key="k.id" :value="k.id">{{ k.nama_kategori }}</option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label">Harga (Rp)</label>
              <input type="number" v-model="form.harga_barang" class="form-control" required min="0">
            </div>

            <div class="form-group">
              <label class="form-label">Stok</label>
              <input type="number" v-model="form.stok" class="form-control" required min="0">
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Deskripsi</label>
            <textarea v-model="form.deskripsi" class="form-control" rows="3"></textarea>
          </div>

          <div class="d-flex gap-2" style="justify-content: flex-end; margin-top: 1.5rem;">
            <button type="button" @click="showForm = false" class="btn btn-secondary">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>

      <!-- Tabel Data Produk -->
      <div class="glass-card table-responsive">
        <div v-if="loading" class="text-center text-muted" style="padding: 2rem;">Memuat data...</div>
        <table v-else>
          <thead>
            <tr>
              <th>ID</th>
              <th>Nama Barang</th>
              <th>Kategori</th>
              <th>Harga</th>
              <th>Stok</th>
              <th style="text-align: right;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="produk in produks" :key="produk.id">
              <td>{{ produk.id }}</td>
              <td><strong>{{ produk.nama_barang }}</strong></td>
              <td>{{ produk.kategori?.nama_kategori || '-' }}</td>
              <td>Rp {{ produk.harga_barang.toLocaleString('id-ID') }}</td>
              <td>
                <span :class="{'text-danger': produk.stok === 0}" style="font-weight: 500;">
                  {{ produk.stok }}
                </span>
              </td>
              <td style="text-align: right;">
                <div class="d-flex gap-2" style="justify-content: flex-end;">
                  <button @click="openEditForm(produk)" class="btn btn-secondary" style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">Edit</button>
                  <button @click="hapusProduk(produk.id)" class="btn btn-danger" style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">Hapus</button>
                </div>
              </td>
            </tr>
            <tr v-if="produks.length === 0">
              <td colspan="6" class="text-center text-muted" style="padding: 2rem;">Belum ada data produk.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
.text-danger {
  color: #ef4444;
}
</style>