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
      <h2 class="mb-4">{{ isEditing ? 'Editar Prova' : 'Nova Prova' }}</h2>

      <form @submit.prevent="saveExam" class="card p-4">
        <div class="mb-4">
          <label class="form-label">Título da Prova *</label>
          <input
            v-model="form.title"
            type="text"
            class="form-control"
            required
            placeholder="Ex: Prova de Matemática - Capítulo 3"
          />
        </div>

        <div class="mb-4">
          <label class="form-label">Descrição</label>
          <textarea
            v-model="form.description"
            class="form-control"
            placeholder="Descrição adicional sobre a prova"
            rows="3"
          ></textarea>
        </div>

        <div class="mb-4">
          <label class="form-label">Nota Mínima para Aprovação</label>
          <input
            v-model.number="form.passing_grade"
            type="number"
            class="form-control"
            min="0"
            max="100"
            placeholder="60"
          />
        </div>

        <div class="mb-4">
          <h4 class="mb-3">Questões</h4>

          <div
            v-for="(question, qIndex) in form.questions"
            :key="qIndex"
            class="card mb-3 p-3"
            style="background-color: var(--bg-secondary);"
          >
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="mb-0 text-primary">Questão {{ qIndex + 1 }}</h5>
              <button
                type="button"
                @click="removeQuestion(qIndex)"
                class="btn btn-outline-danger btn-sm"
              >
                ✕
              </button>
            </div>

            <div class="mb-3">
              <label class="form-label">Enunciado *</label>
              <textarea
                v-model="question.text"
                class="form-control"
                required
                placeholder="Digite o enunciado da questão"
                rows="2"
              ></textarea>
            </div>

            <div class="mb-2">
              <label class="form-label">Alternativas</label>

              <div
                v-for="(alt, aIndex) in question.alternatives"
                :key="aIndex"
                class="d-flex align-items-center gap-2 mb-2"
              >
                <input
                  type="radio"
                  class="form-check-input"
                  :value="aIndex"
                  v-model.number="question.correctIndex"
                  :name="`question-${qIndex}`"
                  style="margin-top: 0;"
                />
                <input
                  v-model="alt.text"
                  type="text"
                  class="form-control form-control-sm"
                  placeholder="Digite a alternativa"
                  required
                />
                <button
                  type="button"
                  @click="removeAlternative(qIndex, aIndex)"
                  class="btn btn-outline-danger btn-sm"
                  :disabled="question.alternatives.length <= 2"
                >
                  ✕
                </button>
              </div>

              <button
                type="button"
                @click="addAlternative(qIndex)"
                class="btn btn-outline-secondary btn-sm w-100 mt-2"
              >
                + Adicionar alternativa
              </button>
            </div>
          </div>

          <button
            type="button"
            @click="addQuestion"
            class="btn btn-outline-primary w-100 mt-3"
          >
            + Adicionar Questão
          </button>
        </div>

        <div class="d-flex gap-3 pt-4 border-top">
          <button type="submit" class="btn btn-primary">
            {{ isEditing ? 'Salvar Alterações' : 'Criar Prova' }}
          </button>
          <router-link to="/professor" class="btn btn-outline-secondary">
            Cancelar
          </router-link>
        </div>
      </form>
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

const isEditing = computed(() => !!route.params.id)
const examId = computed(() => route.params.id)
const currentUser = computed(() => auth.user)

const form = ref({
  title: '',
  description: '',
  passing_grade: 60,
  questions: [],
})

onMounted(async () => {
  if (isEditing.value) {
    await loadExam()
  } else {
    addQuestion()
  }
})

const loadExam = async () => {
  try {
    const response = await api.get(`/exams/${examId.value}`)
    const exam = response.data

    form.value = {
      title: exam.title,
      description: exam.description || '',
      questions: exam.questions.map((q) => ({
        id: q.id,
        text: q.content,
        order: q.order,
        alternatives: q.alternatives.map((a) => ({
          id: a.id,
          text: a.text,
          is_correct: a.is_correct,
        })),
        correctIndex: q.alternatives.findIndex((a) => a.is_correct),
      })),
    }
  } catch (error) {
    console.error('Erro ao carregar prova:', error)
    alert('Erro ao carregar prova')
    router.push('/professor')
  }
}

const addQuestion = () => {
  form.value.questions.push({
    text: '',
    alternatives: [
      { text: '', is_correct: true },
      { text: '', is_correct: false },
      { text: '', is_correct: false },
      { text: '', is_correct: false },
    ],
    correctIndex: 0,
  })
}

const removeQuestion = (index) => {
  form.value.questions.splice(index, 1)
}

const addAlternative = (questionIndex) => {
  form.value.questions[questionIndex].alternatives.push({
    text: '',
    is_correct: false,
  })
}

const removeAlternative = (questionIndex, altIndex) => {
  const alternatives = form.value.questions[questionIndex].alternatives
  if (alternatives.length > 2) {
    alternatives.splice(altIndex, 1)
  } else {
    alert('Uma questão deve ter pelo menos 2 alternativas')
  }
}

const saveExam = async () => {
  try {
    for (let q of form.value.questions) {
      if (q.correctIndex === undefined || q.correctIndex === null) {
        alert('Cada questão deve ter uma alternativa marcada como correta')
        return
      }
    }

    const examData = {
      title: form.value.title,
      description: form.value.description,
      passing_grade: form.value.passing_grade,
    }

    let exam
    if (isEditing.value) {
      await api.put(`/exams/${examId.value}`, examData)
      exam = { id: examId.value }
    } else {
      const response = await api.post('/exams', examData)
      exam = response.data
    }

    for (let qIndex = 0; qIndex < form.value.questions.length; qIndex++) {
      const q = form.value.questions[qIndex]

      const alternatives = q.alternatives.map((alt, aIndex) => ({
        text: alt.text,
        is_correct: aIndex === q.correctIndex,
      }))

      const questionData = {
        text: q.text,
        order: qIndex + 1,
        alternatives,
      }

      if (q.id) {
        await api.put(`/questions/${q.id}`, questionData)
      } else {
        await api.post(`/exams/${exam.id}/questions`, questionData)
      }
    }

    alert(isEditing.value ? 'Prova atualizada com sucesso!' : 'Prova criada com sucesso!')
    router.push('/professor')
  } catch (error) {
    console.error('Erro ao salvar:', error)
    alert('Erro ao salvar prova')
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

.border-top {
  border-color: var(--border-color) !important;
}

.form-check-input:checked {
  background-color: var(--accent);
  border-color: var(--accent);
}
</style>