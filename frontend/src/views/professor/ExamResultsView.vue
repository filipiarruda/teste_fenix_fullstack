<template>
  <div class="min-vh-100">
    <nav class="navbar navbar-dark">
      <div class="container">
        <div class="d-flex justify-content-between w-100 align-items-center">
          <div>
            <router-link to="/professor" class="btn btn-outline-light btn-sm">← Voltar</router-link>
          </div>
          <div class="d-flex align-items-center gap-3">
            <span class="text-white">{{ currentUser?.name }}</span>
            <button @click="logout" class="btn btn-outline-light btn-sm">Sair</button>
          </div>
        </div>
      </div>
    </nav>

    <div class="container py-4">
      <h2 class="mb-4">{{ examTitle }}</h2>

      <div class="row g-4 mb-4">
        <div class="col-md-4">
          <div class="card">
            <div class="card-body text-center">
              <div class="text-muted small text-uppercase mb-2">Média da Turma</div>
              <div class="fs-2 fw-bold text-primary">{{ classAverage.toFixed(1) }}%</div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card">
            <div class="card-body text-center">
              <div class="text-muted small text-uppercase mb-2">Melhor Pontuação</div>
              <div class="fs-2 fw-bold text-success">{{ topScore ? topScore.percentage.toFixed(1) : 'N/A' }}%</div>
              <small v-if="topScore && topScore.user" class="text-muted">{{ topScore.user.name }}</small>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card">
            <div class="card-body text-center">
              <div class="text-muted small text-uppercase mb-2">Total de Submissões</div>
              <div class="fs-2 fw-bold text-primary">{{ totalSubmissions }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Ranking de Alunos</h5>
        </div>
        <div class="card-body p-0">
          <div v-if="loadingRanking" class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Carregando...</span>
            </div>
            <p class="mt-2 text-muted">Carregando ranking...</p>
          </div>

          <div v-else-if="ranking.length === 0" class="alert alert-info m-3">
            Nenhum aluno respondeu esta prova ainda.
          </div>

          <table v-else class="table table-hover mb-0">
            <thead>
              <tr>
                <th class="text-center" style="width: 60px;">#</th>
                <th>Aluno</th>
                <th class="text-center">Acertos</th>
                <th class="text-center">Pontuação</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(result, index) in ranking" :key="result.id">
                <td class="text-center fw-bold">{{ index + 1 }}</td>
                <td>{{ result.user?.name || 'Desconhecido' }}</td>
                <td class="text-center">{{ result.correct_answers }}/{{ result.total_questions }}</td>
                <td class="text-center fw-bold" :class="result.percentage >= 70 ? 'text-success' : 'text-danger'">
                  {{ result.percentage.toFixed(1) }}%
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import api from '../../services/api'

const router = useRouter()
const route = useRoute()
const auth = useAuthStore()

const examId = computed(() => route.params.id)
const examTitle = ref('')
const classAverage = ref(0)
const topScore = ref(null)
const ranking = ref([])
const totalSubmissions = ref(0)
const loadingRanking = ref(false)
const currentUser = computed(() => auth.user)

onMounted(async () => {
  await fetchAnalytics()
  await fetchRanking()
})

const fetchAnalytics = async () => {
  try {
    const [analyticsRes, topRes] = await Promise.all([
      api.get(`/analytics/exam/${examId.value}`),
      api.get(`/analytics/exam/${examId.value}/top`),
    ])

    const analytics = analyticsRes.data
    examTitle.value = analytics.exam_title
    classAverage.value = analytics.average_score || 0
    totalSubmissions.value = analytics.total_submissions || 0
    topScore.value = topRes.data
  } catch (error) {
    console.error('Erro ao carregar analytics:', error)
    alert('Erro ao carregar dados')
  }
}

const fetchRanking = async () => {
  loadingRanking.value = true
  try {
    const response = await api.get(`/analytics/exam/${examId.value}/ranking`)
    ranking.value = response.data
  } catch (error) {
    console.error('Erro ao carregar ranking:', error)
  } finally {
    loadingRanking.value = false
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
}

.card-header {
  background-color: var(--bg-secondary) !important;
  border-color: var(--border-color) !important;
}

.table > :not(caption) > * > * {
  border-color: var(--border-color);
}
</style>