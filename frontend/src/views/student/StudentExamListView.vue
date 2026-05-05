<template>
  <div>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
      <div class="container-fluid">
        <RouterLink to="/aluno" class="navbar-brand fw-bold">
          📚 Fenix Educação
        </RouterLink>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
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
      <div class="container py-5">
        <div class="row">
          <div class="col-lg-8">
            <div class="mb-5">
              <h1 class="mb-2">Bem-vindo, {{ auth.user?.name }}!</h1>
              <p class="text-muted">Responda as provas disponíveis e veja seu desempenho.</p>
            </div>

            <!-- Carregando -->
            <div v-if="loading" class="text-center py-5">
              <div class="spinner-border" role="status">
                <span class="visually-hidden">Carregando...</span>
              </div>
            </div>

            <!-- Listagem de Provas -->
            <div v-else-if="exams.length > 0" class="row">
              <div v-for="exam in exams" :key="exam.id" class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                  <div class="card-body">
                    <h5 class="card-title">{{ exam.title }}</h5>
                    <p class="card-text text-muted">{{ exam.description || 'Sem descrição' }}</p>
                    <p class="small text-secondary">
                      📝 {{ exam.questions?.length || 0 }} questões
                    </p>
                  </div>
                  <div class="card-footer bg-transparent">
                    <button 
                      @click="startExam(exam.id)"
                      class="btn btn-primary w-100"
                      :disabled="exam.id === respondingExamId"
                    >
                      <span v-if="exam.id === respondingExamId">
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Carregando...
                      </span>
                      <span v-else>Responder Prova</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Vazio -->
            <div v-else class="alert alert-info text-center py-5">
              <p>🎉 Parabéns! Você já respondeu todas as provas disponíveis.</p>
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
                <p class="mb-0">
                  <strong>Email:</strong><br/>
                  {{ auth.user?.email }}
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
import { ref, onMounted } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

const router = useRouter()
const auth = useAuthStore()

const exams = ref([])
const loading = ref(true)
const respondingExamId = ref(null)

onMounted(async () => {
  try {
    const response = await api.get('/student/exams')
    exams.value = response.data
  } catch (error) {
    console.error('Erro ao carregar provas:', error)
  } finally {
    loading.value = false
  }
})

const startExam = async (examId) => {
  respondingExamId.value = examId
  try {
    // Navegar para a página de responder prova
    router.push(`/aluno/exams/${examId}`)
  } catch (error) {
    console.error('Erro ao carregar prova:', error)
    respondingExamId.value = null
  }
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
