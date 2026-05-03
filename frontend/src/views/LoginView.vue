<template>
  <div class="d-flex justify-content-center align-items-center vh-100 bg-dark">
    <div class="card shadow-lg" style="width: 100%; max-width: 400px;">
      <div class="card-body p-4">
        <h1 class="text-center mb-2 text-white">Fenix Educação</h1>
        <p class="text-center text-muted mb-4">Plataforma de Provas Online</p>

        <form @submit.prevent="login">
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input
              v-model="form.email"
              type="email"
              class="form-control"
              placeholder="seu@email.com"
              required
            />
            <div v-if="errors.email" class="text-danger small mt-1">{{ errors.email }}</div>
          </div>

          <div class="mb-3">
            <label class="form-label">Senha</label>
            <input
              v-model="form.password"
              type="password"
              class="form-control"
              placeholder="Sua senha"
              required
            />
            <div v-if="errors.password" class="text-danger small mt-1">{{ errors.password }}</div>
          </div>

          <button type="submit" class="btn btn-primary w-100" :disabled="isLoading">
            {{ isLoading ? 'Entrando...' : 'Entrar' }}
          </button>

          <div v-if="errors.general" class="alert alert-danger mt-3 mb-0">
            {{ errors.general }}
          </div>
        </form>

        <p class="text-center mt-4 mb-0">
          <span class="text-muted">Não tem conta?</span>
          <router-link to="/register" class="text-primary"> Cadastre-se aqui</router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

const router = useRouter()
const auth = useAuthStore()

const isLoading = ref(false)
const form = ref({
  email: '',
  password: ''
})
const errors = ref({
  email: '',
  password: '',
  general: ''
})

const login = async () => {
  errors.value = { email: '', password: '', general: '' }
  isLoading.value = true

  try {
    const response = await api.login({
      email: form.value.email,
      password: form.value.password
    })

    auth.setUser(response.data.user)
    auth.setToken(response.data.token)

    const redirectPath = response.data.user.role === 'professor' ? '/professor' : '/student'
    router.push(redirectPath)
  } catch (error) {
    if (error.response?.data?.errors) {
      const apiErrors = error.response.data.errors
      errors.value.email = apiErrors.email?.[0] || ''
      errors.value.password = apiErrors.password?.[0] || ''
    } else if (error.response?.status === 401) {
      errors.value.general = 'Email ou senha incorretos'
    } else {
      errors.value.general = error.response?.data?.error || 'Erro ao fazer login. Tente novamente.'
    }
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
.card {
  background-color: var(--bg-card) !important;
}

h1 {
  color: var(--text-primary);
  font-size: 24px;
}
</style>