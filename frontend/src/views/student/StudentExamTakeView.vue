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

    <main class="bg-light min-vh-100">
      <div class="container py-5">
        <!-- Mensagem de erro se já respondida -->
        <div v-if="alreadyAnswered" class="alert alert-warning alert-dismissible fade show" role="alert">
          <h4 class="alert-heading">Prova já respondida!</h4>
          <p>Você já respondeu essa prova. Para revisar seu desempenho, 
            <RouterLink :to="`/aluno/exams/${examId}/result`">clique aqui</RouterLink>.
          </p>
          <hr>
          <RouterLink to="/aluno/exams" class="btn btn-sm btn-secondary">
            ← Voltar às provas
          </RouterLink>
        </div>

        <!-- Carregando -->
        <div v-else-if="loading" class="text-center py-5">
          <div class="spinner-border" role="status">
            <span class="visually-hidden">Carregando...</span>
          </div>
        </div>

        <!-- Formulário de Prova -->
        <div v-else class="row">
          <div class="col-lg-8">
            <div class="card shadow-sm">
              <div class="card-header bg-light">
                <h2 class="mb-1">{{ exam.title }}</h2>
                <p class="text-muted mb-0">{{ exam.description }}</p>
              </div>

              <div class="card-body">
                <form @submit.prevent="submitExam">
                  <!-- Erros gerais -->
                  <div v-if="errors.general" class="alert alert-danger mb-4">
                    {{ errors.general }}
                  </div>

                  <!-- Questões -->
                  <div v-for="(question, qIndex) in exam.questions" :key="question.id" class="mb-5 pb-4 border-bottom">
                    <div class="mb-3">
                      <h5>{{ qIndex + 1 }}. {{ question.text }}</h5>
                      <small class="text-danger" v-if="errors[`answer_${qIndex}`]">
                        {{ errors[`answer_${qIndex}`] }}
                      </small>
                    </div>

                    <!-- Opções -->
                    <div class="ps-3">
                      <div v-for="(option, oIndex) in question.options" :key="option.id" class="form-check mb-2">
                        <input 
                          :id="`option_${qIndex}_${oIndex}`"
                          class="form-check-input"
                          type="radio"
                          :name="`question_${qIndex}`"
                          :value="option.id"
                          v-model="answers[qIndex]"
                        />
                        <label :for="`option_${qIndex}_${oIndex}`" class="form-check-label">
                          {{ option.text }}
                        </label>
                      </div>
                    </div>
                  </div>

                  <!-- Botões -->
                  <div class="row gap-3">
                    <div class="col-auto">
                      <RouterLink to="/aluno/exams" class="btn btn-secondary">
                        ← Cancelar
                      </RouterLink>
                    </div>
                    <div class="col-auto">
                      <button 
                        type="submit" 
                        class="btn btn-primary"
                        :disabled="submitting"
                      >
                        <span v-if="submitting">
                          <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                          Enviando...
                        </span>
                        <span v-else>Enviar Respostas</span>
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <!-- Barra lateral - Progresso -->
          <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-light">
                <h6 class="mb-0">Seu Progresso</h6>
              </div>
              <div class="card-body">
                <p class="mb-3">
                  <strong>Total de questões:</strong><br/>
                  {{ exam.questions?.length || 0 }}
                </p>
                <p class="mb-0">
                  <strong>Respondidas:</strong><br/>
                  {{ answeredCount }} / {{ exam.questions?.length || 0 }}
                </p>
                <div class="progress mt-3">
                  <div 
                    class="progress-bar" 
                    role="progressbar"
                    :style="{ width: progressPercentage + '%' }"
                    :aria-valuenow="answeredCount"
                    aria-valuemin="0"
                    :aria-valuemax="exam.questions?.length || 0"
                  ></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { RouterLink, useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

const router = useRouter()
const route = useRoute()
const auth = useAuthStore()

const examId = route.params.id
const exam = ref(null)
const answers = ref({})
const loading = ref(true)
const submitting = ref(false)
const errors = ref({})
const alreadyAnswered = ref(false)

onMounted(async () => {
  try {
    const response = await api.get(`/student/exams/${examId}`)
    exam.value = response.data
    
    // Inicializar respostas como null
    exam.value.questions?.forEach((_, index) => {
      answers.value[index] = null
    })
  } catch (error) {
    if (error.response?.status === 403) {
      alreadyAnswered.value = true
    } else {
      console.error('Erro ao carregar prova:', error)
      errors.value.general = 'Erro ao carregar a prova. Tente novamente.'
    }
  } finally {
    loading.value = false
  }
})

const answeredCount = computed(() => {
  return Object.values(answers.value).filter(answer => answer !== null).length
})

const progressPercentage = computed(() => {
  const total = exam.value?.questions?.length || 0
  if (total === 0) return 0
  return Math.round((answeredCount.value / total) * 100)
})

const submitExam = async () => {
  errors.value = {}
  
  // Validar se todas as questões foram respondidas
  const total = exam.value?.questions?.length || 0
  if (answeredCount.value < total) {
    errors.value.general = 'Por favor, responda todas as questões antes de enviar.'
    return
  }

  submitting.value = true
  try {
    // Construir payload com as respostas
    const answersArray = Object.entries(answers.value).map(([index, optionId]) => ({
      question_id: exam.value.questions[index].id,
      selected_option_id: optionId ? parseInt(optionId) : null
    }))

    const response = await api.post(`/student/exams/${examId}/submit`, {
      answers: answersArray
    })

    // Redirecionar para resultado
    router.push(`/aluno/exams/${examId}/result`)
  } catch (error) {
    if (error.response?.data?.message) {
      errors.value.general = error.response.data.message
    } else if (error.response?.status === 403) {
      alreadyAnswered.value = true
    } else {
      errors.value.general = 'Erro ao submeter respostas. Tente novamente.'
    }
    console.error('Erro ao submeter:', error)
  } finally {
    submitting.value = false
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
