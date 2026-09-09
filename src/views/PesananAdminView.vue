<script setup>
    import { ref, onMounted, computed } from 'vue'
    import AdminLayout from '../components/AdminLayout.vue'
    import api from '../utils/api'

    // -------------------------------------------------------------------
    // STATE (Variabel Reaktif)
    // -------------------------------------------------------------------

    const pesanans = ref([])    // Daftar semua pesanan dari API
    const pelanggans = ref([])  // Daftar pelanggan (untuk dropdown di form)
    const produks = ref([])     // Daftar produk (untuk dropdown di baris item)
    const loading = ref(true)

    // State untuk mengontrol tampilan: form, detail, dan mode edit
    const showForm = ref(false)
    const showDetail = ref(false)    // Panel detail pesanan
    const isEdit = ref(false)
    const selectedPesanan = ref(null) // Menyimpan pesanan yang sedang dilihat detailnya

    // Form utama (Master Data + Detail/Items)
    const form = ref({
        id: null,
        id_pelanggan: '',
        tanggal: new Date().toISOString().split('T')[0],  // Default: YYYY-MM-DD
        items: []  // Menampung baris-baris produk
    })

    // -------------------------------------------------------------------
    // FUNGSI FETCH (Ambil Data dari API)
    // -------------------------------------------------------------------

    // Ambil semua data pesanan
    const fetchPesanans = async () => {
        loading.value = true
        try {
            const response = await api.get('/pesanan')
            pesanans.value = response.data.data || response.data
        } catch (error) {
            console.error("Gagal mengambil pesanan", error)
        } finally {
            loading.value = false
        }
    }

    // Ambil daftar pelanggan
    const fetchPelanggans = async () => {
        try {
            const response = await api.get('/pelanggan')
            pelanggans.value = response.data.data || response.data
        } catch (error) {
            console.error("Gagal mengambil pelanggan", error)
        }
    }

    // Ambil daftar produk
    const fetchProduk = async () => {
        try {
            const response = await api.get('/produk')
            produks.value = response.data.data || response.data
        } catch (error) {
            console.error("Gagal mengambil produk", error)
        }
    }

    onMounted(() => {
        fetchPelanggans()
        fetchProduk()
        fetchPesanans()
    })

    // -------------------------------------------------------------------
    // FUNGSI BUKA FORM (Tambah, Edit, Detail)
    // -------------------------------------------------------------------

    const openAddForm = () => {
        isEdit.value = false
        showDetail.value = false
        form.value = {
            id: null,
            id_pelanggan: pelanggans.value[0]?.id || '',
            tanggal: new Date().toISOString().split('T')[0],
            items: [{ id_produk: '', jumlah: 1 }]
        }
        showForm.value = true
    }

    const openEditForm = (pesanan) => {
        isEdit.value = true
        showDetail.value = false
        form.value = {
            id: pesanan.id,
            id_pelanggan: pesanan.id_pelanggan,
            tanggal: pesanan.tanggal,
            items: pesanan.produk && pesanan.produk.length > 0
                ? pesanan.produk.map(p => ({ id_produk: p.id, jumlah: p.pivot.jumlah }))
                : [{ id_produk: '', jumlah: 1 }]
        }
        showForm.value = true
    }

    const openDetail = (pesanan) => {
        selectedPesanan.value = pesanan
        showForm.value = false
        showDetail.value = true
    }

    const closeDetail = () => {
        showDetail.value = false
        selectedPesanan.value = null
    }

    // -------------------------------------------------------------------
    // FUNGSI MULTIPLE INSERT (Tambah & Hapus Baris Produk)
    // -------------------------------------------------------------------

    const addItem = () => {
        form.value.items.push({ id_produk: '', jumlah: 1 })
    }

    const removeItem = (index) => {
        if (form.value.items.length > 1) {
            form.value.items.splice(index, 1)
        } else {
            alert("Pesanan harus memiliki setidaknya 1 produk.")
        }
    }

    // -------------------------------------------------------------------
    // COMPUTED & HELPER FUNCTIONS
    // -------------------------------------------------------------------

    const totalHarga = computed(() => {
        return form.value.items.reduce((total, item) => {
            const p = produks.value.find(prod => prod.id === item.id_produk)
            const harga = p ? p.harga_barang : 0
            return total + (harga * item.jumlah)
        }, 0)
    })

    const getHargaSatuan = (id_produk) => {
        const p = produks.value.find(prod => prod.id === id_produk)
        return p ? p.harga_barang : 0
    }

    const isProdukDisabled = (id_produk, currentIndex) => {
        return form.value.items.some((item, idx) => idx !== currentIndex && item.id_produk === id_produk)
    }

    const formatRupiah = (angka) => {
        return 'Rp ' + Number(angka || 0).toLocaleString('id-ID')
    }

    // -------------------------------------------------------------------
    // FUNGSI SIMPAN & HAPUS
    // -------------------------------------------------------------------

    const savePesanan = async () => {
        const valid = form.value.items.every(item => item.id_produk !== '')
        if (!valid) {
            alert("Silakan pilih produk untuk semua baris pesanan.")
            return
        }

        try {
            if (isEdit.value) {
                await api.put(`/pesanan/${form.value.id}`, form.value)
            } else {
                await api.post('/pesanan', form.value)
            }
            showForm.value = false
            await fetchPesanans()
        } catch (error) {
            alert("Gagal menyimpan pesanan: " + (error.response?.data?.message || error.message))
        }
    }

    const hapusPesanan = async (id) => {
        if (confirm('Yakin ingin menghapus pesanan ini?')) {
            try {
                await api.delete(`/pesanan/${id}`)
                await fetchPesanans()
            } catch (error) {
                alert("Gagal menghapus pesanan")
            }
        }
    }
</script>

<template>
    <AdminLayout>
        <div class="fade-in">
            <!-- Header Halaman -->
            <div class="d-flex justify-between align-center" style="margin-bottom: 1.5rem;">
                <h2>Manajemen Pesanan</h2>
                <button v-if="!showForm" @click="openAddForm" class="btn btn-primary">+ Buat Pesanan</button>
            </div>

            <!-- SECTION: Panel Detail Pesanan -->
            <div v-if="showDetail && selectedPesanan" class="glass-card mb-4" style="margin-bottom: 2rem;">
                <div class="d-flex justify-between align-center mb-4">
                    <h3>Detail Pesanan</h3>
                    <button @click="closeDetail" class="btn btn-secondary">Tutup</button>
                </div>

                <div class="grid grid-cols-2" style="margin-bottom: 1.5rem;">
                    <div>
                        <p class="text-muted" style="margin-bottom: 0.25rem;">Pelanggan</p>
                        <strong>{{ selectedPesanan.pelanggan?.nama_pelanggan || '-' }}</strong>
                    </div>
                    <div>
                        <p class="text-muted" style="margin-bottom: 0.25rem;">Tanggal Pesanan</p>
                        <strong>{{ selectedPesanan.tanggal }}</strong>
                    </div>
                </div>

                <div class="table-responsive">
                    <table style="margin-bottom: 1rem;">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th style="text-align: center;">Jumlah</th>
                                <th style="text-align: right;">Harga Satuan</th>
                                <th style="text-align: right;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(prod, idx) in selectedPesanan.produk" :key="idx">
                                <td>{{ prod.nama_barang }}</td>
                                <td style="text-align: center;">{{ prod.pivot.jumlah }}</td>
                                <td style="text-align: right;">{{ formatRupiah(prod.harga_barang) }}</td>
                                <td style="text-align: right; font-weight: 500;">
                                    {{ formatRupiah(prod.harga_barang * prod.pivot.jumlah) }}
                                </td>
                            </tr>
                            <tr v-if="!selectedPesanan.produk || selectedPesanan.produk.length === 0">
                                <td colspan="4" class="text-center text-muted" style="padding: 1rem;">Tidak ada detail
                                    produk.</td>
                            </tr>
                        </tbody>
                        <tfoot v-if="selectedPesanan.produk && selectedPesanan.produk.length > 0">
                            <tr>
                                <td colspan="3" style="text-align: right; font-weight: 600;">Total Keseluruhan:</td>
                                <td
                                    style="text-align: right; font-weight: 700; color: var(--primary-color); font-size: 1.1rem;">
                                    {{ formatRupiah(selectedPesanan.produk.reduce((sum, prod) => sum +
                                    (prod.harga_barang * prod.pivot.jumlah), 0)) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- SECTION: Form Tambah / Edit Pesanan -->
            <div v-if="showForm" class="glass-card mb-4" style="margin-bottom: 2rem;">
                <h3>{{ isEdit ? 'Edit Pesanan' : 'Buat Pesanan Baru' }}</h3>
                <form @submit.prevent="savePesanan" style="margin-top: 1.5rem;">
                    <div class="grid grid-cols-2" style="margin-bottom: 2rem;">
                        <div class="form-group">
                            <label class="form-label">Pelanggan</label>
                            <select v-model="form.id_pelanggan" class="form-control" required>
                                <option value="" disabled>Pilih Pelanggan</option>
                                <option v-for="p in pelanggans" :key="p.id" :value="p.id">{{ p.nama_pelanggan }}
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tanggal Pesanan</label>
                            <input type="date" v-model="form.tanggal" class="form-control" required>
                        </div>
                    </div>

                    <hr style="border: 1px solid var(--border-color); margin-bottom: 1.5rem;">

                    <div class="d-flex justify-between align-center mb-4">
                        <h4 style="margin: 0;">Daftar Produk</h4>
                        <button type="button" @click="addItem" class="btn btn-secondary" style="padding: 0.5rem 1rem;">+
                            Tambah Baris</button>
                    </div>

                    <div
                        style="background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 8px; border: 1px solid var(--border-color); margin-bottom: 1.5rem;">
                        <div v-for="(item, index) in form.items" :key="index" class="d-flex align-center gap-3"
                            style="margin-bottom: 1rem;">
                            <div style="flex: 2;">
                                <label v-if="index === 0" class="form-label">Pilih Produk</label>
                                <select v-model="item.id_produk" class="form-control" required>
                                    <option value="" disabled>Pilih Produk</option>
                                    <option v-for="prod in produks" :key="prod.id" :value="prod.id"
                                        :disabled="isProdukDisabled(prod.id, index)">
                                        {{ prod.nama_barang }} - {{ formatRupiah(prod.harga_barang) }} (Stok: {{
                                        prod.stok }})
                                    </option>
                                </select>
                            </div>

                            <div style="flex: 1;">
                                <label v-if="index === 0" class="form-label">Jumlah</label>
                                <input type="number" v-model="item.jumlah" class="form-control" min="1" required>
                            </div>

                            <div style="flex: 1;">
                                <label v-if="index === 0" class="form-label">Subtotal</label>
                                <div
                                    style="padding: 0.75rem; background: var(--bg-light); border-radius: 8px; font-weight: 500;">
                                    {{ formatRupiah(getHargaSatuan(item.id_produk) * item.jumlah) }}
                                </div>
                            </div>

                            <div>
                                <button type="button" @click="removeItem(index)" class="btn btn-danger"
                                    title="Hapus Baris" style="padding: 0.6rem 0.8rem;">
                                    🗑️
                                </button>
                            </div>
                        </div>

                        <div class="d-flex justify-between align-center mt-4"
                            style="border-top: 1px dashed var(--border-color); padding-top: 1rem;">
                            <span style="font-size: 1.1rem; font-weight: 500; color: var(--text-muted);">Total
                                Keseluruhan:</span>
                            <span style="font-size: 1.5rem; font-weight: 700; color: var(--primary-color);">{{
                                formatRupiah(totalHarga) }}</span>
                        </div>
                    </div>

                    <div class="d-flex gap-2" style="justify-content: flex-end; margin-top: 1.5rem;">
                        <button type="button" @click="showForm = false" class="btn btn-secondary">Batal</button>
                        <button type="submit" class="btn btn-primary"
                            style="padding: 0.75rem 2rem; font-size: 1.1rem;">Simpan Pesanan</button>
                    </div>
                </form>
            </div>

            <!-- SECTION: Tabel Daftar Semua Pesanan -->
            <div class="glass-card table-responsive">
                <div v-if="loading" class="text-center text-muted" style="padding: 2rem;">Memuat data...</div>
                <table v-else>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Total Produk</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(psn, index) in pesanans" :key="psn.id">
                            <td>#{{ index + 1 }}</td>
                            <td>{{ psn.tanggal }}</td>
                            <td><strong>{{ psn.pelanggan?.nama_pelanggan || '-' }}</strong></td>
                            <td>{{ psn.produk?.length || 0 }} macam produk</td>
                            <td style="text-align: right;">
                                <div class="d-flex gap-2" style="justify-content: flex-end;">
                                    <button @click="openDetail(psn)" class="btn btn-secondary"
                                        style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">Detail</button>
                                    <button @click="openEditForm(psn)" class="btn btn-secondary"
                                        style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">Edit</button>
                                    <button @click="hapusPesanan(psn.id)" class="btn btn-danger"
                                        style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">Hapus</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="pesanans.length === 0">
                            <td colspan="5" class="text-center text-muted" style="padding: 2rem;">Belum ada pesanan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
    .mb-4 {
        margin-bottom: 1.5rem;
    }

    .mt-4 {
        margin-top: 1.5rem;
    }
</style>