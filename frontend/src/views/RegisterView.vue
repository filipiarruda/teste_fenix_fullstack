<template>
  <div class="auth-page d-flex align-items-center justify-content-center min-vh-100">
    <div class="auth-card card p-4 p-md-5">
      <div class="text-center mb-4">
        <div class="brand-icon mb-3">
          <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="24" cy="24" r="24" fill="#3b82f6" fill-opacity="0.15"/>
            <path d="M24 10L36 18V30L24 38L12 30V18L24 10Z" stroke="#3b82f6" stroke-width="2" fill="none"/>
            <path d="M24 16L30 20V28L24 32L18 28V20L24 16Z" fill="#3b82f6" fill-opacity="0.5"/>
          </svg>
        </div>
        <h1 class="h4 fw-bold text-white mb-1">Fenix Educação</h1>
        <p class="text-muted small">Crie sua conta</p>
      </div>

      <form @submit.prevent="handleRegister" novalidate>
        <div class="mb-3">
          <label for="name" class="form-label text-light-emphasis">Nome completo</label>
          <input
            id="name"
            v-model="form.name"
            type="text"
            class="form-control auth-input"
            placeholder="Seu nome"
            autocomplete="name"
            required
          />
        </div>

        <div class="mb-3">
          <label for="email" class="form-label text-light-emphasis">E-mail</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            class="form-control auth-input"
            placeholder="seu@email.com"
            autocomplete="email"
            required
          />
        </div>

        <div class="mb-3">
          <label for="password" class="form-label text-light-emphasis">Senha</label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            class="form-control auth-input"
            placeholder="Mínimo 8 caracteres"
            autocomplete="new-password"
            required
          />
        </div>

        <div class="mb-4">
          <label for="password_confirmation" class="form-label text-light-emphasis">Confirmar senha</label>
          <input
            id="password_confirmation"
            v-model="form.password_confirmation"
            type="password"
            class="form-control auth-input"
            placeholder="Repita a senha"
            autocomplete="new-password"
            required
          />
        </div>

        <div class="mb-4">
          <label class="form-label text-light-emphasis d-block">Tipo de conta</label>
          <div class="role-selector d-flex gap-3">
            <label class="role-option flex-fill" :class="{ active: form.role === 'professor' }">
              <input type="radio" v-model="form.role" value="professor" class="visually-hidden" />
              <div class="role-content text-center p-3">
                <div class="role-icon mb-2">👨‍🏫</div>
                <div class="fw-semibold">Professor</div>
                <div class="small text-muted">Criar e gerenciar provas</div>
              </div>
            </label>
            <label class="role-option flex-fill" :class="{ active: form.role === 'student' }">
              <input type="radio" v-model="form.role" value="student" class="visually-hidden" />
              <div class="role-content text-center p-3">
                <div class="role-icon mb-2">👨‍🎓</div>
                <div class="fw-semibold">Aluno</div>
                <div class="small text-muted">Realizar provas</div>
              </div>
            </label>
          </div>
        </div>

        <div v-if="error" class="alert alert-danger py-2 small" role="alert">
          {{ error }}
        </div>

        <button
          type="submit"
          class="btn btn-primary w-100 fw-semibold py-2"
          :disabled="loading"
        >
          <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
          {{ loading ? 'Cadastrando...' : 'Criar conta' }}
        </button>
      </form>

      <p class="text-center text-muted small mt-4 mb-0">
        Já tem uma conta?
        <RouterLink to="/" class="link-primary fw-semibold text-decoration-none">Entrar</RouterLink>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: 'student',
})
const loading = ref(false)
const error = ref('')

async function handleRegister() {
  error.value = ''

  if (form.value.password !== form.value.password_confirmation) {
    error.value = 'As senhas não conferem.'
    return
  }

  loading.value = true
  try {
    const user = await auth.register(form.value)
    router.push(user.role === 'professor' ? '/professor' : '/aluno')
  } catch (err) {
    const errors = err.response?.data?.errors
    if (errors) {
      error.value = Object.values(errors).flat().join(' ')
    } else {
      error.value = err.response?.data?.message || 'Erro ao realizar cadastro.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.auth-page {
  background: var(--bg-primary);
}

.auth-card {
  background: var(--bg-card);
  border: 1px solid var(--bs-border-color);
  border-radius: 1rem;
  width: 100%;
  max-width: 480px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
}

.auth-input {
  background: var(--bg-secondary);
  border-color: var(--bs-border-color);
  color: var(--text-primary);
  padding: 0.6rem 0.85rem;
}

.auth-input:focus {
  background: var(--bg-secondary);
  border-color: var(--bs-primary);
  color: var(--text-primary);
  box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
}

.auth-input::placeholder {
  color: var(--text-muted);
}

.form-label {
  font-size: 0.875rem;
  font-weight: 500;
}

.role-option {
  cursor: pointer;
  border-radius: 0.5rem;
  border: 2px solid var(--bs-border-color);
  background: var(--bg-secondary);
  transition: border-color 0.2s, background 0.2s;
}

.role-option:hover {
  border-color: var(--bs-primary);
}

.role-option.active {
  border-color: var(--bs-primary);
  background: rgba(59, 130, 246, 0.1);
}

.role-icon {
  font-size: 1.5rem;
}

.btn-primary {
  background-color: var(--bs-primary);
  border-color: var(--bs-primary);
}
</style>
