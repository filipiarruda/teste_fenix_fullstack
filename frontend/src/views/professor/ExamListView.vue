<template>
  <div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="mb-0">Minhas Provas</h1>
      <RouterLink to="/professor/exams/new" class="btn btn-primary">
        + Nova Prova
      </RouterLink>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Carregando...</span>
      </div>
    </div>

    <!-- Listagem -->
    <div v-else-if="exams.length > 0" class="row">
      <div v-for="exam in exams" :key="exam.id" class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <h5 class="card-title">{{ exam.title }}</h5>
            <p class="card-text text-muted">{{ exam.description || 'Sem descrição' }}</p>
            <p class="small text-secondary">
              {{ exam.questions?.length || 0 }} questões
            </p>
          </div>
          <div class="card-footer bg-transparent">
            <RouterLink 
              :to="`/professor/exams/${exam.id}/edit`" 
              class="btn btn-sm btn-outline-primary me-2"
            >
              ✎ Editar
            </RouterLink>
            <button 
              @click="deleteExam(exam.id)"
              class="btn btn-sm btn-outline-danger"
            >
              🗑 Deletar
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Vazio -->
    <div v-else class="alert alert-info text-center py-5">
      <p>Você ainda não criou nenhuma prova.</p>
      <RouterLink to="/professor/exams/new" class="btn btn-primary">
        Criar Primeira Prova
      </RouterLink>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import api from '@/services/api'

const router = useRouter()
const exams = ref([])
const loading = ref(true)

onMounted(async () => {
  try {
    const response = await api.get('/exams')
    exams.value = response.data
  } catch (error) {
    console.error('Erro ao carregar provas:', error)
  } finally {
    loading.value = false
  }
})

const deleteExam = async (id) => {
  if (confirm('Tem certeza que deseja deletar esta prova?')) {
    try {
      await api.delete(`/exams/${id}`)
      exams.value = exams.value.filter(e => e.id !== id)
      alert('Prova deletada com sucesso!')
    } catch (error) {
      console.error('Erro ao deletar prova:', error)
      alert('Erro ao deletar prova: ' + (error.response?.data?.message || error.message))
    }
  }
}
</script>

<style scoped>
.card {
  border: none;
  transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
  transform: translateY(-4px);
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>
