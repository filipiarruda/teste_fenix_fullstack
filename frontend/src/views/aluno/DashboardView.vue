<template>
  <div>
    <nav class="navbar navbar-expand-lg navbar-dark">
      <div class="container-fluid">
        <router-link class="navbar-brand" to="/student">Fenix Educação</router-link>
        <div class="d-flex align-items-center">
          <span class="text-white me-3">{{ currentUser?.name }}</span>
          <button @click="logout" class="btn btn-outline-light btn-sm">Sair</button>
        </div>
      </div>
    </nav>

    <div class="container py-4">
      <h2 class="mb-4">Provas Disponíveis</h2>

      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Carregando...</span>
        </div>
        <p class="mt-2 text-muted">Carregando provas...</p>
      </div>

      <div v-else-if="exams.length === 0" class="alert alert-info">
        Nenhuma prova disponível no momento.
      </div>

      <div v-else class="row g-4">
        <div v-for="exam in exams" :key="exam.id" class="col-md-6 col-lg-4">
          <div class="card h-100">
            <div class="card-body">
              <h5 class="card-title">{{ exam.title }}</h5>
              <p v-if="exam.description" class="card-text text-muted small">
                {{ exam.description }}
              </p>

              <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="badge bg-secondary">{{ exam.questions?.length || 0 }} questões</span>
                <span
                  v-if="getExamResult(exam)"
                  class="badge"
                  :class="getExamResult(exam).passed ? 'bg-success' : 'bg-danger'"
                >
                  {{ getExamResult(exam).passed ? 'Aprovado' : 'Reprovado' }}
                </span>
                <span v-else class="badge bg-warning text-dark">Não respondida</span>
              </div>

              <template v-if="getExamResult(exam)">
                <div class="text-center mb-3 p-3 bg-dark rounded">
                  <div class="fs-3 fw-bold text-primary">
                    {{ getExamResult(exam).score.toFixed(1) }}%
                  </div>
                </div>
                <button @click="viewResult(exam.id)" class="btn btn-outline-primary w-100">
                  Ver Resultado
                </button>
              </template>
              <template v-else>
                <router-link
                  :to="`/aluno/exam/${exam.id}`"
                  class="btn btn-primary w-100"
                >
                  Iniciar Prova
                </router-link>
              </template>
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

const getExamResult = (exam) => {
  const results = exam.results || exam.exam_results
  if (results && results.length > 0) {
    return results.find(r => r.student_id === auth.user.id) || results[0]
  }
  return null
}

const viewResult = (examId) => {
  router.push(`/aluno/exam/${examId}/result`)
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
  transition: transform 0.2s;
}

.card:hover {
  transform: translateY(-4px);
}

.card-title {
  color: var(--text-primary);
}
</style>