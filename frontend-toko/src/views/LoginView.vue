<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../utils/api'

const router = useRouter()
const email = ref('')
const password = ref('')
const errorMsg = ref('')
const loading = ref(false)

const login = async () => {
  loading.value = true
  errorMsg.value = ''

  try {
    const response = await api.post('/login', {
      email: email.value,
      password: password.value
    })

    // Asumsi API mengembalikan { token: '...' }
    if (response.data.token) {
      localStorage.setItem('token', response.data.token)
      router.push('/admin')   // Redirect ke dashboard admin
    } else {
      errorMsg.value = "Login gagal. Token tidak diterima."
    }
  } catch (error) {
    errorMsg.value = error.response?.data?.message || "Email atau password salah."
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="login-page">
    <div class="login-container glass-card fade-in">
      <div class="text-center" style="margin-bottom: 2rem;">
        <h2 style="margin-bottom: 0.5rem">Welcome Back</h2>
        <p class="text-muted">Login ke dashboard admin Toko Sederhana</p>
      </div>

      <div v-if="errorMsg" class="alert alert-danger">
        {{ errorMsg }}
      </div>

      <form @submit.prevent="login">
        <div class="form-group">
          <label class="form-label">Email Address</label>
          <input type="email" v-model="email" class="form-control" required placeholder="admin@example.com">
        </div>

        <div class="form-group" style="margin-bottom: 2rem;">
          <label class="form-label">Password</label>
          <input type="password" v-model="password" class="form-control" required placeholder="••••••••">
        </div>

        <button type="submit" class="btn btn-primary w-100" :disabled="loading">
          {{ loading ? 'Sedang Login...' : 'Login' }}
        </button>
      </form>

      <div class="text-center" style="margin-top: 1.5rem;">
        <router-link to="/" class="text-muted" style="font-size: 0.875rem;">&larr; Kembali ke Beranda</router-link>
      </div>
    </div>
  </div>
</template>

<style scoped>
.login-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(236, 72, 153, 0.1) 100%);
  padding: 1.5rem;
}

.login-container {
  width: 100%;
  max-width: 400px;
  padding: 2.5rem 2rem;
}

.w-100 {
  width: 100%;
}

.alert {
  padding: 0.75rem 1rem;
  border-radius: var(--radius-md);
  margin-bottom: 1.5rem;
  font-size: 0.875rem;
}

.alert-danger {
  background-color: rgba(239, 68, 68, 0.1);
  color: #ef4444;
  border: 1px solid rgba(239, 68, 68, 0.2);
}
</style>