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
        <!-- Carregando -->
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border" role="status">
            <span class="visually-hidden">Carregando...</span>
          </div>
        </div>

        <!-- Resultado -->
        <div v-else class="row">
          <div class="col-lg-8">
            <!-- Card de Pontuação -->
            <div class="card shadow-sm mb-4">
              <div class="card-header bg-light">
                <h2 class="mb-1">{{ result?.exam?.title }}</h2>
                <p class="text-muted mb-0">Resultado da sua prova</p>
              </div>

              <div class="card-body text-center">
                <div class="row">
                  <div class="col-md-6 border-end py-4">
                    <h3>Sua Pontuação</h3>
                    <div class="display-4 fw-bold mb-2">{{ Math.round(result.score) }}%</div>
                    <p class="text-muted">{{ result.correct_answers }} de {{ result.total_questions }} acertos</p>
                  </div>

                  <div class="col-md-6 py-4">
                    <h3>Status</h3>
                    <div :class="['badge', result.score >= 60 ? 'bg-success' : 'bg-danger']" class="p-3 fs-6">
                      {{ result.score >= 60 ? '✓ Aprovado' : '✗ Reprovado' }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Revisão de Respostas -->
            <div class="card shadow-sm">
              <div class="card-header bg-light">
                <h5 class="mb-0">Revisão das Questões</h5>
              </div>

              <div class="card-body">
                <div v-for="(answer, index) in result.answers" :key="answer.id" class="mb-5 pb-4 border-bottom">
                  <!-- Questão -->
                  <div class="mb-3">
                    <h6 class="mb-2">
                      <span :class="['badge', answer.is_correct ? 'bg-success' : 'bg-danger']" class="me-2">
                        {{ answer.is_correct ? '✓' : '✗' }}
                      </span>
                      {{ index + 1 }}. {{ answer.question.text }}
                    </h6>
                  </div>

                  <!-- Opções com destaque -->
                  <div class="ps-3">
                    <div v-for="option in answer.question.options" :key="option.id" class="mb-2">
                      <div 
                        :class="getOptionClass(option.id, answer.selected_option_id, option.is_correct)"
                        class="p-2 rounded"
                      >
                        <strong v-if="option.is_correct">✓ </strong>
                        <span v-if="option.id === answer.selected_option_id">→ </span>
                        {{ option.text }}
                      </div>
                    </div>
                  </div>

                  <!-- Explicação -->
                  <div v-if="!answer.is_correct" class="alert alert-info mt-3 mb-0">
                    <small>
                      <strong>Resposta correta:</strong>
                      {{ getCorrectOptionText(answer.question.options) }}
                    </small>
                  </div>
                </div>
              </div>
            </div>

            <!-- Botões de Ação -->
            <div class="mt-4">
              <RouterLink to="/aluno" class="btn btn-primary me-2">
                ← Voltar ao Dashboard
              </RouterLink>
              <RouterLink to="/aluno/exams" class="btn btn-outline-secondary">
                Ver Outras Provas →
              </RouterLink>
            </div>
          </div>

          <!-- Barra lateral - Resumo -->
          <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-light">
                <h6 class="mb-0">Resumo</h6>
              </div>
              <div class="card-body">
                <p class="mb-2">
                  <strong>Acertos:</strong><br/>
                  <span class="text-success">{{ result.correct_answers }}</span>
                </p>
                <p class="mb-2">
                  <strong>Erros:</strong><br/>
                  <span class="text-danger">{{ result.total_questions - result.correct_answers }}</span>
                </p>
                <p class="mb-2">
                  <strong>Total:</strong><br/>
                  {{ result.total_questions }}
                </p>
                <hr/>
                <p class="mb-0">
                  <strong>Percentual:</strong><br/>
                  <span class="fs-5">{{ Math.round(result.score) }}%</span>
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
import { RouterLink, useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

const router = useRouter()
const route = useRoute()
const auth = useAuthStore()

const examId = route.params.id
const exam = ref(null)
const result = ref(null)
const loading = ref(true)

onMounted(async () => {
  try {
    const response = await api.get(`/student/exams/${examId}/result`)
    result.value = response.data
    
    // Carregar dados da prova
    const examResponse = await api.get(`/exams/${examId}`, { 
      // Nota: isso pode precisar de um endpoint específico para professores
      // Por enquanto vamos pegar do resultado
    })
  } catch (error) {
    console.error('Erro ao carregar resultado:', error)
  } finally {
    loading.value = false
  }
})

const getOptionClass = (optionId, selectedOptionId, isCorrect) => {
  if (optionId === selectedOptionId && isCorrect) {
    return 'bg-success bg-opacity-10 border border-success'
  } else if (optionId === selectedOptionId && !isCorrect) {
    return 'bg-danger bg-opacity-10 border border-danger'
  } else if (isCorrect) {
    return 'bg-success bg-opacity-10 border border-success'
  }
  return 'bg-light border'
}

const getCorrectOptionText = (options) => {
  return options.find(o => o.is_correct)?.text || 'N/A'
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
