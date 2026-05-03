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

    <div class="container py-4" v-if="!submitted">
      <h2 class="mb-3">{{ examTitle }}</h2>

      <div class="mb-4">
        <span class="text-muted">Questão {{ currentQuestion + 1 }} de {{ totalQuestions }}</span>
        <div class="progress mt-2" style="height: 8px;">
          <div
            class="progress-bar bg-primary"
            role="progressbar"
            :style="{ width: ((currentQuestion + 1) / totalQuestions) * 100 + '%' }"
          ></div>
        </div>
      </div>

      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Carregando...</span>
        </div>
        <p class="mt-2 text-muted">Carregando prova...</p>
      </div>

      <template v-else-if="questions.length > 0">
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title mb-4">Questão {{ currentQuestion + 1 }}</h5>
            <p class="mb-4">{{ questions[currentQuestion].text }}</p>

            <div class="d-flex flex-column gap-3">
              <label
                v-for="(alt, index) in questions[currentQuestion].alternatives"
                :key="alt.label"
                class="p-3 border rounded cursor-pointer"
                :class="{ 'border-primary bg-primary': answers[questions[currentQuestion].id] === alt.label }"
                style="cursor: pointer;"
              >
                <div class="form-check">
                  <input
                    class="form-check-input"
                    type="radio"
                    :name="`question-${questions[currentQuestion].id}`"
                    :value="alt.label"
                    v-model="answers[questions[currentQuestion].id]"
                  />
                  <span class="ms-2">{{ alt.text }}</span>
                </div>
              </label>
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-between">
          <button
            v-if="currentQuestion > 0"
            @click="currentQuestion--"
            class="btn btn-outline-secondary"
          >
            ← Anterior
          </button>
          <div v-else></div>

          <button
            v-if="currentQuestion < totalQuestions - 1"
            @click="currentQuestion++"
            class="btn btn-outline-secondary"
          >
            Próxima →
          </button>

          <button
            v-else
            @click="submitExam"
            class="btn btn-success"
          >
            ✓ Entregar Prova
          </button>
        </div>
      </template>
    </div>

    <!-- Submission confirmation -->
    <div class="container py-5 text-center" v-else-if="!resultLoaded">
      <div class="card d-inline-block p-5">
        <h3 class="mb-3">Prova Enviada!</h3>
        <p class="text-muted mb-4">Sua prova foi enviada com sucesso.</p>
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Carregando...</span>
        </div>
        <p class="mt-3 text-muted">Carregando resultado...</p>
      </div>
    </div>

    <!-- Results -->
    <div class="container py-4" v-else>
      <h2 class="mb-4">Seu Resultado</h2>

      <div class="card mb-4">
        <div class="card-body text-center">
          <div class="display-4 fw-bold text-primary mb-2">
            {{ result.percentage.toFixed(1) }}%
          </div>
          <p class="text-muted mb-0">Seu desempenho</p>
        </div>
      </div>

      <div class="row mb-4">
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

      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">Gabarito</h5>
        </div>
        <div class="card-body">
          <div v-for="(question, index) in questions" :key="question.id" class="border-bottom mb-3 pb-3">
            <p class="mb-2"><strong>Q{{ index + 1 }}:</strong> {{ question.text }}</p>
            <div class="row">
              <div class="col-md-6 mb-2">
                <small class="text-muted d-block">Sua resposta:</small>
                <span>{{ getAnswerText(question, answers[question.id]) || 'Não respondida' }}</span>
              </div>
              <div class="col-md-6 mb-2">
                <small class="text-muted d-block">Resposta correta:</small>
                <span class="text-success">{{ getCorrectAnswerText(question) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <router-link to="/aluno" class="btn btn-primary">
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
const examTitle = ref('')
const questions = ref([])
const answers = ref({})
const currentQuestion = ref(0)
const loading = ref(false)
const submitted = ref(false)
const resultLoaded = ref(false)
const result = ref(null)
const currentUser = computed(() => auth.user)

const totalQuestions = computed(() => questions.value.length)

const letters = ['A', 'B', 'C', 'D']

onMounted(async () => {
  await loadExam()
})

const loadExam = async () => {
  loading.value = true
  try {
    const response = await api.get(`/exams/${examId.value}`)
    const exam = response.data

    examTitle.value = exam.title

    questions.value = exam.questions.map((q) => {
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

    questions.value.forEach((q) => {
      answers.value[q.id] = null
    })
  } catch (error) {
    console.error('Erro ao carregar prova:', error)
    alert('Erro ao carregar prova')
    router.push('/aluno')
  } finally {
    loading.value = false
  }
}

const getAnswerText = (question, label) => {
  if (!label) return ''
  const alt = question.alternatives.find((a) => a.label === label)
  return alt ? alt.text : ''
}

const getCorrectAnswerText = (question) => {
  const alt = question.alternatives.find((a) => a.label === question.correct_answer)
  return alt ? alt.text : ''
}

const submitExam = async () => {
  const unanswered = questions.value.some((q) => !answers.value[q.id])

  if (unanswered) {
    alert('Por favor, responda todas as questões antes de entregar')
    return
  }

  const confirmation = confirm('Tem certeza que deseja entregar a prova? Você não poderá modificar suas respostas depois.')

  if (!confirmation) return

  submitted.value = true

  try {
    const submissionData = {
      exam_id: examId.value,
      answers: questions.value.map((q) => ({
        question_id: q.id,
        selected_answer: answers.value[q.id]
      }))
    }

    const submitResponse = await api.post('/submissions/submit', submissionData)
    result.value = submitResponse.data
    resultLoaded.value = true
  } catch (error) {
    console.error('Erro ao submeter:', error)
    alert('Erro ao submeter prova: ' + (error.response?.data?.error || error.response?.data?.message || error.message))
    submitted.value = false
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

.border-primary {
  border-color: var(--accent) !important;
}

.bg-primary {
  background-color: var(--accent) !important;
}

.border {
  border-color: var(--border-color) !important;
}

.border-bottom {
  border-color: var(--border-color) !important;
}

.form-check-input:checked {
  background-color: var(--accent);
  border-color: var(--accent);
}

.cursor-pointer {
  cursor: pointer;
}
</style>