<template>
  <div>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
      <div class="container-fluid">
        <RouterLink to="/professor" class="navbar-brand fw-bold">
          📚 Fenix Educação
        </RouterLink>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item">
              <RouterLink to="/professor" class="nav-link">
                Dashboard
              </RouterLink>
            </li>
            <li class="nav-item">
              <RouterLink to="/professor/exams" class="nav-link">
                Minhas Provas
              </RouterLink>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                {{ auth.user?.name }}
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li>
                  <a class="dropdown-item" href="#" @click.prevent="logout">
                    Sair
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Conteúdo -->
    <main class="bg-light min-vh-100">
      <RouterView />
    </main>
  </div>
</template>

<script setup>
import { RouterLink, RouterView, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

const router = useRouter()
const auth = useAuthStore()

const logout = async () => {
  try {
    await api.post('/auth/logout')
    auth.logout()
    router.push('/')
  } catch (error) {
    console.error('Erro ao fazer logout:', error)
    auth.logout()
    router.push('/')
  }
}
</script>

<style scoped>
.min-vh-100 {
  min-height: 100vh;
}
</style>
