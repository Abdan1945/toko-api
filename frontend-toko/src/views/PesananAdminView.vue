<script setup>
    import { ref, onMounted } from 'vue'
    import AdminLayout from '../components/AdminLayout.vue'
    import api from '../utils/api'

    const pesanans = ref([])
    const pelanggans = ref([])
    const produks = ref([])
    const loading = ref(true)
    const selectedPesanan = ref(null)
    const showDetail = ref(false)
    const showForm = ref(false)

    const form = ref({
        id_pelanggan: '',
        tanggal: '',
        items: [{ id_produk: '', jumlah: 1 }]
    })

    const fetchPesanan = async () => {
        loading.value = true
        try {
            const response = await api.get('/pesanan')
            const resData = response?.data

            if (resData?.data?.data && Array.isArray(resData.data.data)) {
                pesanans.value = resData.data.data
            } else if (Array.isArray(resData?.data)) {
                pesanans.value = resData.data
            } else if (Array.isArray(resData)) {
                pesanans.value = resData
            } else {
                pesanans.value = []
            }
        } catch (error) {
            console.error("Gagal mengambil pesanan", error)
            pesanans.value = []
        } finally {
            loading.value = false
        }
    }

    const fetchDropdownData = async () => {
        try {
            const [resPelanggan, resProduk] = await Promise.all([
                api.get('/pelanggan'),
                api.get('/produk')
            ])

            const dataPelanggan = resPelanggan?.data?.data || resPelanggan?.data || []
            const dataProduk = resProduk?.data?.data || resProduk?.data || []

            pelanggans.value = Array.isArray(dataPelanggan) ? dataPelanggan : []
            produks.value = Array.isArray(dataProduk) ? dataProduk : []
        } catch (error) {
            console.error("Gagal mengambil data dropdown", error)
        }
    }

    onMounted(() => {
        fetchPesanan()
        fetchDropdownData()
    })

    const tambahItem = () => {
        form.value.items.push({ id_produk: '', jumlah: 1 })
    }

    const hapusItem = (index) => {
        if (form.value.items.length > 1) {
            form.value.items.splice(index, 1)
        }
    }

    const openAddForm = () => {
        form.value = {
            id_pelanggan: '',
            tanggal: new Date().toISOString().split('T')[0],
            items: [{ id_produk: '', jumlah: 1 }]
        }
        showForm.value = true
        showDetail.value = false
    }

    const savePesanan = async () => {
        try {
            await api.post('/pesanan', {
                id_pelanggan: form.value.id_pelanggan,
                tanggal: form.value.tanggal,
                items: form.value.items
            })
            showForm.value = false
            fetchPesanan()
        } catch (error) {
            alert("Gagal menambah pesanan: " + (error.response?.data?.message || error.message))
        }
    }

    const openDetail = (pesanan) => {
        selectedPesanan.value = pesanan
        showDetail.value = true
        showForm.value = false
    }

    const closeDetail = () => {
        showDetail.value = false
        selectedPesanan.value = null
    }

    const getNamaPelanggan = (pesanan) => {
        return pesanan?.pelanggan?.nama_pelanggan || '-'
    }

    const getAlamatPelanggan = (pesanan) => {
        return pesanan?.pelanggan?.alamat || '-'
    }

    const getDaftarProduk = (pesanan) => {
        if (!pesanan.produk || pesanan.produk.length === 0) return '-'
        return pesanan.produk.map(p => `${p.nama_barang} (${p.pivot?.jumlah || 0})`).join(', ')
    }
</script>

<template>
    <AdminLayout>
        <div class="fade-in">
            <div class="d-flex justify-between align-center" style="margin-bottom: 1.5rem;">
                <h2>Daftar Pesanan</h2>
                <button v-if="!showForm" @click="openAddForm" class="btn btn-primary">+ Tambah Pesanan</button>
            </div>

            <!-- Form Tambah Pesanan -->
            <div v-if="showForm" class="glass-card mb-4" style="margin-bottom: 2rem;">
                <h3>Tambah Pesanan Baru</h3>
                <form @submit.prevent="savePesanan" style="margin-top: 1.5rem;">
                    <div class="form-group mb-3">
                        <label class="form-label">Pelanggan</label>
                        <select v-model="form.id_pelanggan" class="form-control" required>
                            <option value="">-- Pilih Pelanggan --</option>
                            <option v-for="p in pelanggans" :key="p.id" :value="p.id">
                                {{ p.nama_pelanggan }} — {{ p.alamat }}
                            </option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" v-model="form.tanggal" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Item Produk</label>
                        <div v-for="(item, index) in form.items" :key="index" class="d-flex gap-2"
                            style="margin-bottom: 0.75rem; align-items: center;">
                            <select v-model="item.id_produk" class="form-control" required>
                                <option value="">-- Pilih Produk --</option>
                                <option v-for="prod in produks" :key="prod.id" :value="prod.id">
                                    {{ prod.nama_barang }}
                                </option>
                            </select>
                            <input type="number" v-model.number="item.jumlah" class="form-control" style="width: 100px;"
                                min="1" required>
                            <button type="button" @click="hapusItem(index)" class="btn btn-danger"
                                style="padding: 0.25rem 0.75rem;" :disabled="form.items.length === 1">
                                ×
                            </button>
                        </div>
                        <button type="button" @click="tambahItem" class="btn btn-secondary" style="margin-top: 0.5rem;">
                            + Tambah Item
                        </button>
                    </div>

                    <div class="d-flex gap-2" style="justify-content: flex-end; margin-top: 1.5rem;">
                        <button type="button" @click="showForm = false" class="btn btn-secondary">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>

            <!-- Detail Pesanan -->
            <div v-if="showDetail && selectedPesanan" class="glass-card mb-4" style="margin-bottom: 2rem;">
                <div class="d-flex justify-between align-center" style="margin-bottom: 1rem;">
                    <h3>Detail Pesanan #{{ selectedPesanan.id }}</h3>
                    <button @click="closeDetail" class="btn btn-secondary">Tutup</button>
                </div>

                <div style="margin-bottom: 1rem;">
                    <p><strong>Pelanggan:</strong> {{ getNamaPelanggan(selectedPesanan) }}</p>
                    <p><strong>Alamat:</strong> {{ getAlamatPelanggan(selectedPesanan) }}</p>
                    <p><strong>Tanggal:</strong> {{ selectedPesanan.tanggal }}</p>
                </div>

                <h4 style="margin-bottom: 0.75rem;">Daftar Produk</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="produk in selectedPesanan.produk" :key="produk.id">
                            <td>{{ produk.nama_barang }}</td>
                            <td>Rp {{ Number(produk.harga_barang).toLocaleString('id-ID') }}</td>
                            <td>{{ produk.pivot?.jumlah || 0 }}</td>
                        </tr>
                        <tr v-if="!selectedPesanan.produk || selectedPesanan.produk.length === 0">
                            <td colspan="3" class="text-center text-muted">Tidak ada produk</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Tabel Utama (sudah ada kolom Alamat) -->
            <div class="glass-card table-responsive">
                <div v-if="loading" class="text-center text-muted" style="padding: 2rem;">Memuat data...</div>
                <table v-else>
                    <thead>
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Pelanggan</th>
                            <th>Alamat</th>
                            <th>Tanggal</th>
                            <th>Produk</th>
                            <th style="text-align: right; width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="pesanan in pesanans" :key="pesanan.id">
                            <td>{{ pesanan.id }}</td>
                            <td><strong>{{ getNamaPelanggan(pesanan) }}</strong></td>
                            <td>{{ getAlamatPelanggan(pesanan) }}</td>
                            <td>{{ pesanan.tanggal }}</td>
                            <td>{{ getDaftarProduk(pesanan) }}</td>
                            <td style="text-align: right;">
                                <button @click="openDetail(pesanan)" class="btn btn-secondary"
                                    style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">
                                    Detail
                                </button>
                            </td>
                        </tr>
                        <tr v-if="pesanans.length === 0">
                            <td colspan="6" class="text-center text-muted" style="padding: 2rem;">
                                Belum ada data pesanan.
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