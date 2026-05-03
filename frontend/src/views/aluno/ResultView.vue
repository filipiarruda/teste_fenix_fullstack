<template>
  <div class="min-vh-100">
    <nav class="navbar navbar-dark">
      <div class="container">
        <div class="d-flex justify-content-between w-100 align-items-center">
          <div>
            <router-link to="/aluno" class="btn btn-outline-light btn-sm">← Voltar</router-link>
          </div>
          <div class="d-flex align-items-center gap-3">
            <span class="text-white">{{ currentUser?.name }}</span>
            <button @click="logout" class="btn btn-outline-light btn-sm">Sair</button>
          </div>
        </div>
      </div>
    </nav>

    <div class="container py-4">
      <h2 class="mb-4">Resultado da Prova</h2>

      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Carregando...</span>
        </div>
        <p class="mt-2 text-muted">Carregando resultado...</p>
      </div>

      <div v-else-if="result" class="card mb-4">
        <div class="card-body text-center py-4">
          <div class="display-3 fw-bold mb-2" :class="result.passed ? 'text-success' : 'text-danger'">
            {{ result.score.toFixed(1) }}%
          </div>
          <p class="text-muted mb-0">Seu desempenho</p>
        </div>
      </div>

      <div v-if="result" class="row mb-4">
        <div class="col-md-6">
          <div class="card">
            <div class="card-body text-center">
              <div class="fs-4 fw-bold">{{ result.correct_answers }}/{{ result.total_questions }}</div>
              <div class="text-muted">Acertos</div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card">
            <div class="card-body text-center">
              <div class="fs-4 fw-bold" :class="result.passed ? 'text-success' : 'text-danger'">
                {{ result.passed ? 'Aprovado' : 'Reprovado' }}
              </div>
              <div class="text-muted">Status</div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="result" class="card">
        <div class="card-header">
          <h5 class="mb-0">Gabarito</h5>
        </div>
        <div class="card-body">
          <div v-if="questionsWithAnswers.length === 0" class="text-center py-3 text-muted">
            Carregando gabarito...
          </div>

          <div v-for="(item, index) in questionsWithAnswers" :key="item.question.id" class="border-bottom mb-3 pb-3">
            <p class="mb-2"><strong>Questão {{ index + 1 }}:</strong> {{ item.question.text }}</p>
            <div class="row">
              <div class="col-md-6 mb-2">
                <div
                  class="p-2 rounded"
                  :class="item.isCorrect ? 'bg-success bg-opacity-25 border-success' : 'bg-danger bg-opacity-25 border-danger'"
                  style="border: 1px solid;"
                >
                  <small class="d-block text-muted">Sua resposta:</small>
                  <span :class="item.isCorrect ? 'text-success' : 'text-danger'">
                    {{ item.yourAnswer }}
                  </span>
                  <span
                    v-if="item.isCorrect"
                    class="badge bg-success ms-2"
                  >Correto</span>
                  <span
                    v-else
                    class="badge bg-danger ms-2"
                  >Incorreto</span>
                </div>
              </div>
              <div class="col-md-6 mb-2">
                <div class="p-2 rounded bg-dark" style="border: 1px solid var(--border-color);">
                  <small class="d-block text-muted">Resposta correta:</small>
                  <span class="text-success">{{ item.correctAnswer }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="!result && !loading" class="alert alert-info">
        <p class="mb-0">Resultado não encontrado.</p>
      </div>

      <router-link to="/aluno" class="btn btn-primary mt-4">
        Voltar para Provas
      </router-link>
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
const result = ref(null)
const questions = ref([])
const answers = ref({})
const loading = ref(false)
const currentUser = computed(() => auth.user)

const letters = ['A', 'B', 'C', 'D']

const questionsWithAnswers = computed(() => {
  return questions.value.map((question) => {
    const userAnswerLabel = answers.value[question.id]
    const userAnswer = question.alternatives.find((a) => a.label === userAnswerLabel)
    const correctAlternative = question.alternatives.find((a) => a.label === question.correct_answer)

    return {
      question,
      yourAnswer: userAnswer?.text || 'Não respondida',
      correctAnswer: correctAlternative?.text || 'N/A',
      isCorrect: userAnswerLabel === question.correct_answer,
    }
  })
})

onMounted(async () => {
  await loadResult()
  await loadAnswers()
})

const loadResult = async () => {
  loading.value = true
  try {
    const response = await api.get(
      `/submissions/exam/${examId.value}/result/${auth.user.id}`
    )
    result.value = response.data

    const examResponse = await api.get(`/exams/${examId.value}`)
    const exam = examResponse.data

    questions.value = (exam.questions || []).map((q) => {
      const alternatives = []
      letters.forEach((letter) => {
        const key = `option_${letter.toLowerCase()}`
        if (q[key]) {
          alternatives.push({ label: letter, text: q[key] })
        }
      })
      return {
        id: q.id,
        text: q.content,
        alternatives,
        correct_answer: q.correct_answer
      }
    })
  } catch (error) {
    console.error('Erro ao carregar resultado:', error)
    alert('Erro ao carregar resultado')
  } finally {
    loading.value = false
  }
}

const loadAnswers = async () => {
  try {
    const response = await api.get(
      `/submissions/exam/${examId.value}/user/${auth.user.id}`
    )

    const answersData = response.data
    if (Array.isArray(answersData)) {
      answersData.forEach((answer) => {
        answers.value[answer.question_id] = answer.selected_answer
      })
    }
  } catch (error) {
    console.error('Erro ao carregar respostas:', error)
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

.border-bottom {
  border-color: var(--border-color) !important;
}

.bg-opacity-25 {
  --bs-bg-opacity: 0.25;
}
</style>