<script setup>
    import { ref, onMounted } from 'vue'
    import AdminLayout from '../components/AdminLayout.vue'
    import api from '../utils/api'

    const pelanggans = ref([])
    const loading = ref(true)
    const selectedPelanggan = ref(null)
    const showDetail = ref(false)
    const showForm = ref(false)
    const isEdit = ref(false) // Dynamic state untuk mode edit

    const form = ref({
        id: null,
        nama_pelanggan: '',
        email: '',
        no_telepon: '',
        alamat: ''
    })

    // Ambil daftar pelanggan
    const fetchPelanggan = async () => {
        loading.value = true
        try {
            const response = await api.get('/pelanggan')
            const resData = response?.data

            if (resData?.data?.data && Array.isArray(resData.data.data)) {
                pelanggans.value = resData.data.data
            } else if (Array.isArray(resData?.data)) {
                pelanggans.value = resData.data
            } else if (Array.isArray(resData)) {
                pelanggans.value = resData
            } else {
                pelanggans.value = []
            }
        } catch (error) {
            console.error("Gagal mengambil pelanggan", error)
            pelanggans.value = []
        } finally {
            loading.value = false
        }
    }

    onMounted(() => {
        fetchPelanggan()
    })

    // Buka form tambah
    const openAddForm = () => {
        isEdit.value = false
        form.value = { id: null, nama_pelanggan: '', email: '', no_telepon: '', alamat: '' }
        showForm.value = true
        showDetail.value = false
    }

    // Buka form edit
    const openEditForm = (pelanggan) => {
        isEdit.value = true
        form.value = { ...pelanggan } // Duplicate object pelanggan ke form
        showForm.value = true
        showDetail.value = false
    }

    // Simpan pelanggan (Tambah/Edit)
    const savePelanggan = async () => {
        try {
            if (isEdit.value) {
                await api.put(`/pelanggan/${form.value.id}`, form.value)
            } else {
                await api.post('/pelanggan', form.value)
            }
            showForm.value = false
            fetchPelanggan()
        } catch (error) {
            alert("Gagal menyimpan pelanggan: " + (error.response?.data?.message || error.message))
        }
    }

    // Buka detail
    const openDetail = (pelanggan) => {
        selectedPelanggan.value = pelanggan
        showDetail.value = true
        showForm.value = false
    }

    // Tutup detail
    const closeDetail = () => {
        showDetail.value = false
        selectedPelanggan.value = null
    }
</script>

<template>
    <AdminLayout>
        <div class="fade-in">
            <div class="d-flex justify-between align-center" style="margin-bottom: 1.5rem;">
                <h2>Daftar Pelanggan</h2>
                <button v-if="!showForm" @click="openAddForm" class="btn btn-primary">+ Tambah Pelanggan</button>
            </div>

            <!-- Form Tambah / Edit -->
            <div v-if="showForm" class="glass-card mb-4" style="margin-bottom: 2rem;">
                <h3>{{ isEdit ? 'Edit Data Pelanggan' : 'Tambah Pelanggan Baru' }}</h3>
                <form @submit.prevent="savePelanggan" style="margin-top: 1.5rem;">
                    <div class="form-group mb-3">
                        <label class="form-label">Nama Pelanggan</label>
                        <input type="text" v-model="form.nama_pelanggan" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" v-model="form.email" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">No. Telepon</label>
                        <input type="tel" v-model="form.no_telepon" class="form-control" required>
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

            <!-- Detail Pelanggan -->
            <div v-if="showDetail && selectedPelanggan" class="glass-card mb-4" style="margin-bottom: 2rem;">
                <div class="d-flex justify-between align-center" style="margin-bottom: 1rem;">
                    <h3>Detail Pelanggan #{{ selectedPelanggan.id }}</h3>
                    <button @click="closeDetail" class="btn btn-secondary">Tutup</button>
                </div>

                <div>
                    <p><strong>ID:</strong> {{ selectedPelanggan.id }}</p>
                    <p><strong>Nama Pelanggan:</strong> {{ selectedPelanggan.nama_pelanggan }}</p>
                    <p><strong>Email:</strong> {{ selectedPelanggan.email || '-' }}</p>
                    <p><strong>No. Telepon:</strong> {{ selectedPelanggan.no_telepon || '-' }}</p>
                    <p><strong>Alamat:</strong> {{ selectedPelanggan.alamat }}</p>
                </div>
            </div>

            <!-- Tabel Data Pelanggan -->
            <div class="glass-card table-responsive">
                <div v-if="loading" class="text-center text-muted" style="padding: 2rem;">Memuat data...</div>
                <table v-else>
                    <thead>
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Nama Pelanggan</th>
                            <th>Email</th>
                            <th>No. Telepon</th>
                            <th>Alamat</th>
                            <th style="text-align: right; width: 160px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="pelanggan in pelanggans" :key="pelanggan.id">
                            <td>{{ pelanggan.id }}</td>
                            <td><strong>{{ pelanggan.nama_pelanggan }}</strong></td>
                            <td>{{ pelanggan.email || '-' }}</td>
                            <td>{{ pelanggan.no_telepon || '-' }}</td>
                            <td>{{ pelanggan.alamat }}</td>
                            <td style="text-align: right;">
                                <div class="d-flex gap-2" style="justify-content: flex-end;">
                                    <button @click="openDetail(pelanggan)" class="btn btn-secondary"
                                        style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">
                                        Detail
                                    </button>
                                    <!-- <button @click="openEditForm(pelanggan)" class="btn btn-secondary"
                                        style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">
                                        Edit
                                    </button> -->
                                </div>
                            </td>
                        </tr>
                        <tr v-if="pelanggans.length === 0">
                            <td colspan="6" class="text-center text-muted" style="padding: 2rem;">
                                Belum ada data pelanggan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
    .mb-3 {
        margin-bottom: 1rem;
    }
</style>