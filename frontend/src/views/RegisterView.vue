<template>
  <div class="d-flex justify-content-center align-items-center vh-100 bg-dark p-3">
    <div class="card shadow-lg" style="width: 100%; max-width: 500px;">
      <div class="card-body p-4">
        <!-- Step 1: Role Selection -->
        <div v-if="!role">
          <h1 class="text-center mb-2 text-white">Fenix Educação</h1>
          <p class="text-center text-muted mb-4">Selecione seu perfil</p>

          <div class="d-flex gap-3 mb-3">
            <button
              @click="role = 'professor'"
              class="btn btn-outline-light flex-fill p-4"
            >
              <div class="fs-2 mb-2">👨‍🏫</div>
              <div>Professor</div>
            </button>
            <button
              @click="role = 'student'"
              class="btn btn-outline-light flex-fill p-4"
            >
              <div class="fs-2 mb-2">👨‍🎓</div>
              <div>Aluno</div>
            </button>
          </div>

          <p class="text-center mb-0">
            <span class="text-muted">Já tem conta?</span>
            <router-link to="/" class="text-primary"> Faça login</router-link>
          </p>
        </div>

        <!-- Step 2: Register Form -->
        <div v-if="role">
          <button @click="role = null" class="btn btn-link text-decoration-none mb-3 ps-0 text-primary">
            ← Voltar
          </button>

          <h3 class="mb-4 text-white">
            {{ role === 'professor' ? 'Cadastro - Professor' : 'Cadastro - Aluno' }}
          </h3>

          <form @submit.prevent="register">
            <div class="mb-3">
              <label class="form-label">Nome Completo</label>
              <input
                v-model="form.name"
                type="text"
                class="form-control"
                placeholder="Ex: João Silva"
                required
              />
              <div v-if="errors.name" class="text-danger small mt-1">{{ errors.name }}</div>
            </div>

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
                placeholder="Mínimo 8 caracteres"
                required
              />
              <div v-if="errors.password" class="text-danger small mt-1">{{ errors.password }}</div>
            </div>

            <div class="mb-3">
              <label class="form-label">Confirmar Senha</label>
              <input
                v-model="form.password_confirmation"
                type="password"
                class="form-control"
                placeholder="Repita a senha"
                required
              />
            </div>

            <button type="submit" class="btn btn-primary w-100" :disabled="isLoading">
              {{ isLoading ? 'Cadastrando...' : 'Cadastrar' }}
            </button>

            <div v-if="errors.general" class="alert alert-danger mt-3 mb-0">
              {{ errors.general }}
            </div>
          </form>
        </div>
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

const role = ref(null)
const isLoading = ref(false)
const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})
const errors = ref({
  name: '',
  email: '',
  password: '',
  general: ''
})

const register = async () => {
  errors.value = { name: '', email: '', password: '', general: '' }
  isLoading.value = true

  try {
    const response = await api.register({
      name: form.value.name,
      email: form.value.email,
      password: form.value.password,
      password_confirmation: form.value.password_confirmation,
      role: role.value
    })

    auth.setUser(response.data.user)
    auth.setToken(response.data.token)

    router.push(role.value === 'professor' ? '/professor' : '/student')
  } catch (error) {
    if (error.response?.data?.errors) {
      const apiErrors = error.response.data.errors
      errors.value.name = apiErrors.name?.[0] || ''
      errors.value.email = apiErrors.email?.[0] || ''
      errors.value.password = apiErrors.password?.[0] || ''
    } else {
      errors.value.general = error.response?.data?.message || 'Erro ao cadastrar. Tente novamente.'
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
  font-size: 24px;
}

h3 {
  color: var(--text-primary);
}
</style>