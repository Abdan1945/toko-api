import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import ProdukView from '../views/ProdukView.vue'
import DetailProduk from '../views/DetailProduk.vue'
import LoginView from '../views/LoginView.vue'
import DashboardView from '../views/DashboardView.vue'
import ProdukAdminView from '../views/ProdukAdminView.vue'
import KategoriAdminView from '../views/KategoriAdminView.vue'
import PelangganAdminView from '../views/PelangganAdminView.vue'
import PesananAdminView from '../views/PesananAdminView.vue'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        { path: '/', name: 'home', component: HomeView },
        { path: '/produk', name: 'produk', component: ProdukView },
        { path: '/detail-produk/:id', name: 'detail-produk', component: DetailProduk },
        { path: '/login', name: 'login', component: LoginView },
        {
            path: '/admin',
            name: 'admin-dashboard',
            component: DashboardView,
            meta: { requiresAuth: true }
        },
        {
            path: '/admin/produk',
            name: 'admin-produk',
            component: ProdukAdminView,
            meta: { requiresAuth: true }
        },
        {
            path: '/admin/kategori',
            name: 'admin-kategori',
            component: KategoriAdminView,
            meta: { requiresAuth: true }
        },
         {
            path: '/admin/pelanggan',
            name: 'admin-pelanggan',
            component: PelangganAdminView,
            meta: { requiresAuth: true }
        },
         {
            path: '/admin/pesanan',
            name: 'admin-pesanan',
            component: PesananAdminView,
            meta: { requiresAuth: true }
        }
    ]
});

// Navigation Guard — Satpam penjaga pintu halaman Admin
router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem('token') !== null;

  if (to.meta.requiresAuth && !isAuthenticated) {
    next({ name: 'login' });           // Belum login → tendang ke /login
  } else if (to.name === 'login' && isAuthenticated) {
    next({ name: 'admin-dashboard' }); // Sudah login → masuk dashboard
  } else {
    next();
  }
});

export default router