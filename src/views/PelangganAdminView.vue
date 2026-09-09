<script setup>
import { ref, onMounted } from 'vue'
import AdminLayout from '../components/AdminLayout.vue'
import api from '../utils/api'

// -------------------------------------------------------------------
// STATE (Variabel Reaktif)
// -------------------------------------------------------------------

const pelanggans = ref([])   // Menampung daftar semua pelanggan dari API
const loading = ref(true)    // Untuk menampilkan teks "Memuat data..."

// State form: apakah form ditampilkan? apakah mode edit?
const showForm = ref(false)
const isEdit = ref(false)

// Objek form yang di-binding ke input di template
const form = ref({ id: null, nama_pelanggan: '', alamat: '' })

// -------------------------------------------------------------------
// FUNGSI FETCH (Ambil Data dari API)
// -------------------------------------------------------------------

const fetchPelanggans = async () => {
  loading.value = true
  try {
    const response = await api.get('/pelanggan')
    // Ambil array datanya. Cek dulu apakah responnya pakai wrapper .data atau tidak
    pelanggans.value = response.data.data || response.data
  } catch (error) {
    console.error("Gagal mengambil pelanggan", error)
  } finally {
    loading.value = false  // Hentikan loading apapun hasilnya (sukses/gagal)
  }
}

// Jalankan fetchPelanggans secara otomatis saat komponen pertama kali dimuat
onMounted(() => {
  fetchPelanggans()
})

// -------------------------------------------------------------------
// FUNGSI FORM
// -------------------------------------------------------------------

// Buka form dalam mode TAMBAH (reset semua field ke kosong)
const openAddForm = () => {
  isEdit.value = false
  form.value = { id: null, nama_pelanggan: '', alamat: '' }
  showForm.value = true
}

// Buka form dalam mode EDIT (isi field dengan data pelanggan yang dipilih)
const openEditForm = (pelanggan) => {
  isEdit.value = true
  form.value = { ...pelanggan }   // Spread operator: salin semua properti objek
  showForm.value = true
}

// -------------------------------------------------------------------
// FUNGSI SIMPAN (POST = tambah baru, PUT = update)
// -------------------------------------------------------------------

const savePelanggan = async () => {
  try {
    if (isEdit.value) {
      // Mode Edit: kirim request PUT ke /pelanggan/{id}
      await api.put(`/pelanggan/${form.value.id}`, form.value)
    } else {
      // Mode Tambah: kirim request POST ke /pelanggan
      await api.post('/pelanggan', form.value)
    }
    showForm.value = false    // Tutup form setelah berhasil
    fetchPelanggans()         // Refresh tabel agar data terbaru tampil
  } catch (error) {
    alert("Gagal menyimpan pelanggan")
  }
}

// -------------------------------------------------------------------
// FUNGSI HAPUS (DELETE)
// -------------------------------------------------------------------

const hapusPelanggan = async (id) => {
  // Tampilkan dialog konfirmasi sebelum menghapus
  if (confirm('Yakin ingin menghapus pelanggan ini?')) {
    try {
      await api.delete(`/pelanggan/${id}`)
      fetchPelanggans()   // Refresh tabel setelah berhasil dihapus
    } catch (error) {
      alert("Gagal menghapus pelanggan")
    }
  }
}
</script>

<template>
  <AdminLayout>
    <div class="fade-in">
      <!-- Header Halaman + Tombol Tambah -->
      <div class="d-flex justify-between align-center" style="margin-bottom: 1.5rem;">
        <h2>Manajemen Pelanggan</h2>
        <!-- Tombol hanya muncul saat form sedang disembunyikan (v-if) -->
        <button v-if="!showForm" @click="openAddForm" class="btn btn-primary">+ Tambah Pelanggan</button>
      </div>

      <!-- ============================================================ -->
      <!-- SECTION: Form Tambah / Edit (hanya muncul jika showForm=true) -->
      <!-- ============================================================ -->
      <div v-if="showForm" class="glass-card mb-4" style="margin-bottom: 2rem;">
        <!-- Judul form berubah tergantung mode (tambah/edit) -->
        <h3>{{ isEdit ? 'Edit Pelanggan' : 'Tambah Pelanggan Baru' }}</h3>

        <!-- @submit.prevent agar form tidak melakukan page refresh saat di-submit -->
        <form @submit.prevent="savePelanggan" style="margin-top: 1.5rem;">

          <div class="form-group">
            <label class="form-label">Nama Pelanggan</label>
            <!-- v-model: binding dua arah antara input dan variabel form -->
            <input type="text" v-model="form.nama_pelanggan" class="form-control" required>
          </div>

          <div class="form-group">
            <label class="form-label">Alamat</label>
            <textarea v-model="form.alamat" class="form-control" rows="3" required></textarea>
          </div>

          <div class="d-flex gap-2" style="justify-content: flex-end; margin-top: 1.5rem;">
            <button type="button" @click="showForm = false" class="btn btn-secondary">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>

      <!-- ============================================================ -->
      <!-- SECTION: Tabel Data Pelanggan                                 -->
      <!-- ============================================================ -->
      <div class="glass-card table-responsive">
        <!-- Tampilkan teks loading jika data masih diambil -->
        <div v-if="loading" class="text-center text-muted" style="padding: 2rem;">Memuat data...</div>

        <!-- v-else: tabel baru ditampilkan setelah loading selesai -->
        <table v-else>
          <thead>
            <tr>
              <th style="width: 80px;">ID</th>
              <th>Nama Pelanggan</th>
              <th>Alamat</th>
              <th style="text-align: right; width: 200px;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <!-- v-for: loop setiap pelanggan di array, render satu baris per data -->
            <tr v-for="p in pelanggans" :key="p.id">
              <td>{{ p.id }}</td>
              <td><strong>{{ p.nama_pelanggan }}</strong></td>
              <td>{{ p.alamat || '-' }}</td>
              <td style="text-align: right;">
                <div class="d-flex gap-2" style="justify-content: flex-end;">
                  <button @click="openEditForm(p)" class="btn btn-secondary" style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">Edit</button>
                  <button @click="hapusPelanggan(p.id)" class="btn btn-danger" style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">Hapus</button>
                </div>
              </td>
            </tr>

            <!-- Tampilkan baris kosong jika belum ada data sama sekali -->
            <tr v-if="pelanggans.length === 0">
              <td colspan="4" class="text-center text-muted" style="padding: 2rem;">Belum ada data pelanggan.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>