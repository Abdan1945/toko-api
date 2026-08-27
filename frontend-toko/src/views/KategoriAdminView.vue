<script setup>
    import { ref, onMounted } from 'vue'
    import AdminLayout from '../components/AdminLayout.vue'
    import api from '../utils/api'

    const kategoris = ref([])
    const loading = ref(true)

    // State untuk kontrol form
    const showForm = ref(false)
    const isEdit = ref(false)
    const form = ref({ id: null, nama_kategori: '' })

    // Ambil semua kategori dari API
    const fetchKategoris = async () => {
        loading.value = true
        try {
            const response = await api.get('/kategori')
            kategoris.value = response.data.data
        } catch (error) {
            console.error("Gagal mengambil kategori", error)
        } finally {
            loading.value = false
        }
    }

    onMounted(() => {
        fetchKategoris()
    })

    // Buka form Tambah (reset form dulu)
    const openAddForm = () => {
        isEdit.value = false
        form.value = { id: null, nama_kategori: '' }
        showForm.value = true
    }

    // Buka form Edit (isi form dengan data yang dipilih)
    const openEditForm = (kategori) => {
        isEdit.value = true
        form.value = { ...kategori }   // Salin data kategori ke form
        showForm.value = true
    }

    // Simpan (POST jika tambah, PUT jika edit)
    const saveKategori = async () => {
        try {
            if (isEdit.value) {
                await api.put(`/kategori/${form.value.id}`, form.value)
            } else {
                await api.post('/kategori', form.value)
            }
            showForm.value = false
            fetchKategoris()   // Refresh data!
        } catch (error) {
            alert("Gagal menyimpan kategori")
        }
    }

    // Hapus kategori
    const hapusKategori = async (id) => {
        if (confirm('Yakin ingin menghapus kategori ini?')) {
            try {
                await api.delete(`/kategori/${id}`)
                fetchKategoris()   // Refresh data!
            } catch (error) {
                alert("Gagal menghapus kategori")
            }
        }
    }
</script>

<template>
    <AdminLayout>
        <div class="fade-in">
            <div class="d-flex justify-between align-center" style="margin-bottom: 1.5rem;">
                <h2>Manajemen Kategori</h2>
                <button v-if="!showForm" @click="openAddForm" class="btn btn-primary">+ Tambah Kategori</button>
            </div>

            <!-- Form Tambah / Edit -->
            <div v-if="showForm" class="glass-card mb-4" style="margin-bottom: 2rem;">
                <h3>{{ isEdit ? 'Edit Kategori' : 'Tambah Kategori' }}</h3>
                <form @submit.prevent="saveKategori" class="d-flex align-center gap-3" style="margin-top: 1.5rem;">
                    <input type="text" v-model="form.nama_kategori" class="form-control" style="flex: 1"
                        placeholder="Nama Kategori..." required>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" @click="showForm = false" class="btn btn-secondary">Batal</button>
                </form>
            </div>

            <!-- Tabel Data Kategori -->
            <div class="glass-card table-responsive">
                <div v-if="loading" class="text-center text-muted" style="padding: 2rem;">Memuat data...</div>
                <table v-else>
                    <thead>
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Nama Kategori</th>
                            <th style="text-align: right; width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Tambahkan index di v-for -->
                        <tr v-for="(k, index) in kategoris" :key="k.id">
                            <!-- Tampilkan index + 1 agar dimulai dari angka 1 -->
                            <td>{{ index + 1 }}</td>
                            <td><strong>{{ k.nama_kategori }}</strong></td>
                            <td style="text-align: right;">
                                <div class="d-flex gap-2" style="justify-content: flex-end;">
                                    <button @click="openEditForm(k)" class="btn btn-secondary"
                                        style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">Edit</button>
                                    <button @click="hapusKategori(k.id)" class="btn btn-danger"
                                        style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">Hapus</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="kategoris.length === 0">
                            <td colspan="3" class="text-center text-muted" style="padding: 2rem;">Belum ada kategori.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>