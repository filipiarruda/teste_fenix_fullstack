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
              <a href="#" class="nav-link" @click.prevent="navigateToExams">
                Minhas Provas
              </a>
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

    <!-- Conteúdo Principal -->
    <main class="bg-light min-vh-100">
      <div class="container py-5">
        <div class="row">
          <div class="col-lg-8">
            <div class="mb-5">
              <h1 class="mb-2">Bem-vindo, {{ auth.user?.name }}!</h1>
              <p class="text-muted">Gerencie suas provas e acompanhe o desempenho dos alunos.</p>
            </div>

            <!-- Cards de Ações Rápidas -->
            <div class="row g-4 mb-5">
              <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm text-center">
                  <div class="card-body py-5">
                    <div class="mb-3 text-primary" style="font-size: 2.5rem;">
                      📋
                    </div>
                    <h5 class="card-title">Minhas Provas</h5>
                    <p class="card-text text-muted">Crie, edite e gerencie suas provas</p>
                    <button @click="navigateToExams" class="btn btn-primary btn-sm">
                      Ver Provas
                    </button>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm text-center">
                  <div class="card-body py-5">
                    <div class="mb-3 text-success" style="font-size: 2.5rem;">
                      ➕
                    </div>
                    <h5 class="card-title">Nova Prova</h5>
                    <p class="card-text text-muted">Comece a criar uma nova prova</p>
                    <button @click="navigateToNewExam" class="btn btn-success btn-sm">
                      Criar
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Informações -->
            <div class="alert alert-info">
              <h6 class="alert-heading">💡 Dica</h6>
              <p class="mb-0">
                Crie provas com múltiplas questões e opções. Os alunos poderão respondê-las e receberão 
                feedback instantâneo com o resultado e o gabarito correto.
              </p>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-light">
                <h6 class="mb-0">Seu Perfil</h6>
              </div>
              <div class="card-body">
                <p class="mb-2">
                  <strong>Nome:</strong><br/>
                  {{ auth.user?.name }}
                </p>
                <p class="mb-2">
                  <strong>Email:</strong><br/>
                  {{ auth.user?.email }}
                </p>
                <p class="mb-0">
                  <strong>Cargo:</strong><br/>
                  <span class="badge bg-primary">Professor</span>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { RouterLink, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

const router = useRouter()
const auth = useAuthStore()

const navigateToExams = () => {
  router.push('/professor/exams')
}

const navigateToNewExam = () => {
  router.push('/professor/exams/new')
}

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
