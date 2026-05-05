<template>
  <div class="container py-5">
    <div class="mb-4">
      <RouterLink to="/professor/exams" class="btn btn-outline-secondary">
        ← Voltar
      </RouterLink>
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-8">
        <h1 class="mb-4">{{ isEditing ? 'Editar Prova' : 'Nova Prova' }}</h1>

        <form @submit.prevent="submitForm" class="card shadow">
          <div class="card-body">
            <!-- Informações da Prova -->
            <div class="mb-4 border-bottom pb-4">
              <h5 class="mb-3">Informações da Prova</h5>
              
              <div class="mb-3">
                <label for="title" class="form-label">Título *</label>
                <input 
                  v-model="form.title"
                  type="text"
                  id="title"
                  class="form-control"
                  placeholder="Ex: Prova de Matemática - Unidade 1"
                  required
                />
                <small v-if="errors.title" class="text-danger">{{ errors.title }}</small>
              </div>

              <div class="mb-3">
                <label for="description" class="form-label">Descrição</label>
                <textarea 
                  v-model="form.description"
                  id="description"
                  class="form-control"
                  rows="3"
                  placeholder="Descreva o conteúdo desta prova..."
                />
              </div>
            </div>

            <!-- Questões -->
            <div class="mb-4">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Questões *</h5>
                <button 
                  type="button"
                  @click="addQuestion"
                  class="btn btn-sm btn-success"
                >
                  + Adicionar Questão
                </button>
              </div>

              <!-- Lista de Questões -->
              <div v-for="(question, qIndex) in form.questions" :key="qIndex" class="card mb-3 border-light">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                  <h6 class="mb-0">Questão {{ qIndex + 1 }}</h6>
                  <button 
                    type="button"
                    @click="removeQuestion(qIndex)"
                    class="btn btn-sm btn-outline-danger"
                  >
                    Remover
                  </button>
                </div>

                <div class="card-body">
                  <div class="mb-3">
                    <label :for="`question_${qIndex}`" class="form-label">Texto da Questão *</label>
                    <textarea 
                      v-model="question.text"
                      :id="`question_${qIndex}`"
                      class="form-control"
                      rows="2"
                      placeholder="Digite o enunciado da questão..."
                      required
                    />
                    <small v-if="errors[`questions.${qIndex}.text`]" class="text-danger">
                      {{ errors[`questions.${qIndex}.text`] }}
                    </small>
                  </div>

                  <!-- Opções -->
                  <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <label class="form-label mb-0">Opções de Resposta *</label>
                      <button 
                        type="button"
                        @click="addOption(qIndex)"
                        class="btn btn-sm btn-info"
                      >
                        + Opção
                      </button>
                    </div>

                    <div v-for="(option, oIndex) in question.options" :key="oIndex" class="input-group mb-2">
                      <div class="form-check">
                        <input 
                          v-model="option.is_correct"
                          type="radio"
                          :name="`correct_${qIndex}`"
                          :id="`correct_${qIndex}_${oIndex}`"
                          class="form-check-input"
                        />
                        <label :for="`correct_${qIndex}_${oIndex}`" class="form-check-label">
                          Correta
                        </label>
                      </div>

                      <input 
                        v-model="option.text"
                        type="text"
                        class="form-control"
                        placeholder="Digite a opção..."
                        required
                      />

                      <button 
                        type="button"
                        @click="removeOption(qIndex, oIndex)"
                        class="btn btn-outline-danger"
                      >
                        ✕
                      </button>
                    </div>

                    <small v-if="errors[`questions.${qIndex}.options`]" class="text-danger d-block">
                      {{ errors[`questions.${qIndex}.options`] }}
                    </small>
                  </div>
                </div>
              </div>

              <small v-if="errors.questions" class="text-danger d-block">{{ errors.questions }}</small>
            </div>

            <!-- Botões -->
            <div class="d-flex gap-2 justify-content-end pt-3 border-top">
              <RouterLink to="/professor/exams" class="btn btn-outline-secondary">
                Cancelar
              </RouterLink>
              <button 
                type="submit"
                :disabled="saving"
                class="btn btn-primary"
              >
                {{ saving ? 'Salvando...' : (isEditing ? 'Atualizar' : 'Criar') }} Prova
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { RouterLink, useRouter, useRoute } from 'vue-router'
import api from '@/services/api'

const router = useRouter()
const route = useRoute()

const examId = route.params.id
const isEditing = computed(() => !!examId)

const form = ref({
  title: '',
  description: '',
  questions: [
    {
      text: '',
      order: 0,
      options: [
        { text: '', is_correct: true, order: 0 },
        { text: '', is_correct: false, order: 1 },
      ],
    },
  ],
})

const errors = ref({})
const saving = ref(false)

onMounted(async () => {
  if (isEditing.value) {
    try {
      const response = await api.get(`/exams/${examId}`)
      const exam = response.data
      form.value = {
        title: exam.title,
        description: exam.description,
        questions: exam.questions.map((q, qIdx) => ({
          ...q,
          order: qIdx,
          options: q.options.map((o, oIdx) => ({
            ...o,
            order: oIdx,
          })),
        })),
      }
    } catch (error) {
      console.error('Erro ao carregar prova:', error)
      alert('Erro ao carregar prova')
      router.push('/professor/exams')
    }
  }
})

const addQuestion = () => {
  form.value.questions.push({
    text: '',
    order: form.value.questions.length,
    options: [
      { text: '', is_correct: true, order: 0 },
      { text: '', is_correct: false, order: 1 },
    ],
  })
}

const removeQuestion = (index) => {
  form.value.questions.splice(index, 1)
}

const addOption = (questionIndex) => {
  const question = form.value.questions[questionIndex]
  question.options.push({
    text: '',
    is_correct: false,
    order: question.options.length,
  })
}

const removeOption = (questionIndex, optionIndex) => {
  form.value.questions[questionIndex].options.splice(optionIndex, 1)
}

const submitForm = async () => {
  errors.value = {}
  saving.value = true

  try {
    const payload = {
      title: form.value.title,
      description: form.value.description,
      questions: form.value.questions.map((q, qIdx) => ({
        text: q.text,
        order: qIdx,
        options: q.options.map((o, oIdx) => ({
          text: o.text,
          is_correct: o.is_correct,
          order: oIdx,
        })),
      })),
    }

    if (isEditing.value) {
      await api.put(`/exams/${examId}`, payload)
      alert('Prova atualizada com sucesso!')
    } else {
      await api.post('/exams', payload)
      alert('Prova criada com sucesso!')
    }

    router.push('/professor/exams')
  } catch (error) {
    console.error('Erro ao salvar prova:', error)
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else if (error.response?.data?.message) {
      alert('Erro: ' + error.response.data.message)
    } else {
      alert('Erro ao salvar prova')
    }
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
.input-group .form-check {
  padding: 0.375rem 0.75rem;
  background-color: #f8f9fa;
  border: 1px solid #dee2e6;
  border-radius: 0.25rem 0 0 0.25rem;
}

.input-group .form-check-input {
  margin-top: 0.375rem;
}

.card {
  border: none;
}

.card-header {
  border-bottom: 1px solid #dee2e6;
}
</style>
