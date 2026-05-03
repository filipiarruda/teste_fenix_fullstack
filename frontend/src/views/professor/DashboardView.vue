<template>
  <div class="min-vh-100">
    <nav class="navbar navbar-dark">
      <div class="container-fluid">
        <router-link class="navbar-brand" to="/professor">Fenix Educação</router-link>
        <div class="d-flex align-items-center">
          <span class="text-white me-3">{{ currentUser?.name }}</span>
          <button @click="logout" class="btn btn-outline-light btn-sm">Sair</button>
        </div>
      </div>
    </nav>

    <div class="container py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Minhas Provas</h2>
        <router-link to="/professor/exam/create" class="btn btn-primary">
          Nova Prova
        </router-link>
      </div>

      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Carregando...</span>
        </div>
      </div>

      <div v-else-if="exams.length === 0" class="alert alert-info text-center">
        <p class="mb-3">Você ainda não criou nenhuma prova.</p>
        <router-link to="/professor/exam/create" class="btn btn-primary">
          Criar primeira prova
        </router-link>
      </div>

      <div v-else class="row g-4">
        <div v-for="exam in exams" :key="exam.id" class="col-md-6 col-lg-4">
          <div class="card h-100">
            <div class="card-body">
              <h5 class="card-title">{{ exam.title }}</h5>
              <p v-if="exam.description" class="card-text text-muted small mb-3">
                {{ exam.description }}
              </p>

              <div class="d-flex justify-content-between mb-3 py-2 border-top border-bottom">
                <div class="text-center">
                  <div class="fs-4 fw-bold text-primary">{{ exam.questions?.length || 0 }}</div>
                  <small class="text-muted">Questões</small>
                </div>
                <div class="text-center">
                  <div class="fs-4 fw-bold text-primary">{{ exam.results?.length || 0 }}</div>
                  <small class="text-muted">Respostas</small>
                </div>
              </div>

              <div class="d-flex flex-wrap gap-2">
                <router-link
                  :to="`/professor/exam/${exam.id}/edit`"
                  class="btn btn-outline-secondary btn-sm"
                >
                  Editar
                </router-link>
                <router-link
                  :to="`/professor/exam/${exam.id}/results`"
                  class="btn btn-outline-secondary btn-sm"
                >
                  Resultados
                </router-link>
                <button @click="deleteExam(exam.id)" class="btn btn-outline-danger btn-sm">
                  Deletar
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import api from '../../services/api'

const router = useRouter()
const auth = useAuthStore()

const loading = ref(false)
const exams = ref([])
const currentUser = computed(() => auth.user)

onMounted(async () => {
  await fetchExams()
})

const fetchExams = async () => {
  loading.value = true
  try {
    const response = await api.get('/exams')
    exams.value = response.data
  } catch (error) {
    console.error('Erro ao carregar provas:', error)
    alert('Erro ao carregar provas')
  } finally {
    loading.value = false
  }
}

const deleteExam = async (examId) => {
  if (!confirm('Tem certeza que deseja deletar esta prova?')) return

  try {
    await api.delete(`/exams/${examId}`)
    await fetchExams()
    alert('Prova deletada com sucesso')
  } catch (error) {
    console.error('Erro ao deletar:', error)
    alert('Erro ao deletar prova')
  }
}

const logout = () => {
  auth.logout()
  router.push('/')
}
</script>

<style scoped>
.navbar {
  background-color: var(--bg-secondary);
}

.card {
  background-color: var(--bg-card);
  transition: transform 0.2s;
}

.card:hover {
  transform: translateY(-4px);
}

.card-title {
  color: var(--text-primary);
}

.border-top,
.border-bottom {
  border-color: var(--border-color) !important;
}
</style>