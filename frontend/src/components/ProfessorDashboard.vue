<template>
  <div class="professor-dashboard">
    <h1>Dashboard do Professor</h1>
    <div class="stats-grid">
      <div class="stat-card">
        <h3>{{ totalExams }}</h3>
        <p>Provas Criadas</p>
      </div>
      <div class="stat-card">
        <h3>{{ totalStudents }}</h3>
        <p>Estudantes</p>
      </div>
      <div class="stat-card">
        <h3>{{ totalSubmissions }}</h3>
        <p>Respostas Recebidas</p>
      </div>
      <div class="stat-card">
        <h3>{{ averageScore.toFixed(1) }}%</h3>
        <p>Média Geral</p>
      </div>
    </div>

    <div class="actions">
      <button @click="showCreateExam = true" class="btn-primary">+ Nova Prova</button>
    </div>

    <div class="exams-section">
      <h2>Minhas Provas</h2>
      <table class="exams-table">
        <thead>
          <tr>
            <th>Título</th>
            <th>Questões</th>
            <th>Respostas</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="exam in exams" :key="exam.id">
            <td>{{ exam.title }}</td>
            <td>{{ exam.total_questions }}</td>
            <td>{{ exam.submission_count || 0 }}</td>
            <td>
              <button @click="viewResults(exam)" class="btn-small">Resultados</button>
              <button @click="deleteExam(exam)" class="btn-small btn-danger">Deletar</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showCreateExam" class="modal">
      <div class="modal-content">
        <h2>Criar Nova Prova</h2>
        <form @submit.prevent="createExam">
          <input v-model="newExam.title" placeholder="Título da Prova" required />
          <textarea v-model="newExam.description" placeholder="Descrição"></textarea>
          <input v-model.number="newExam.total_questions" type="number" placeholder="Número de Questões" required />
          <input v-model.number="newExam.passing_grade" type="number" placeholder="Nota Mínima" required />
          <button type="submit" class="btn-primary">Criar Prova</button>
          <button type="button" @click="showCreateExam = false" class="btn-secondary">Cancelar</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import API from '@/services/api'

const exams = ref([])
const totalExams = ref(0)
const totalStudents = ref(0)
const totalSubmissions = ref(0)
const averageScore = ref(0)
const showCreateExam = ref(false)
const newExam = ref({
  title: '',
  description: '',
  total_questions: 5,
  passing_grade: 60
})

onMounted(async () => {
  try {
    const { data } = await API.getExams()
    exams.value = data
    totalExams.value = data.length
    totalStudents.value = 10
    totalSubmissions.value = data.reduce((sum, e) => sum + (e.submission_count || 0), 0)
    averageScore.value = 72.5
  } catch(e) {
    console.error('Failed to load exams:', e)
  }
})

const createExam = async () => {
  try {
    const { data } = await API.createExam({
      ...newExam.value,
      professor_id: 1
    })
    exams.value.push(data)
    showCreateExam.value = false
    newExam.value = { title: '', description: '', total_questions: 5, passing_grade: 60 }
  } catch(e) {
    console.error('Failed to create exam:', e)
  }
}

const viewResults = (exam) => {
  console.log('View results for exam:', exam.id)
}

const deleteExam = async (exam) => {
  if(confirm('Tem certeza que deseja deletar esta prova?')) {
    try {
      await API.deleteExam(exam.id)
      exams.value = exams.value.filter(e => e.id !== exam.id)
    } catch(e) {
      console.error('Failed to delete exam:', e)
    }
  }
}
</script>

<style scoped>
.professor-dashboard {
  padding: 20px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 15px;
  margin: 20px 0;
}

.stat-card {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  padding: 20px;
  border-radius: 8px;
  text-align: center;
}

.stat-card h3 {
  font-size: 2em;
  margin: 0;
}

.stat-card p {
  margin: 10px 0 0 0;
  font-size: 0.9em;
}

.actions {
  margin: 20px 0;
}

.btn-primary {
  background: #667eea;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 500;
}

.btn-primary:hover {
  background: #764ba2;
}

.exams-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

.exams-table th, .exams-table td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

.exams-table th {
  background: #f5f5f5;
  font-weight: 600;
}

.btn-small {
  padding: 6px 12px;
  font-size: 0.85em;
  background: #667eea;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  margin-right: 5px;
}

.btn-small.btn-danger {
  background: #dc3545;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  padding: 30px;
  border-radius: 8px;
  width: 90%;
  max-width: 500px;
}

.modal-content form {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.modal-content input, .modal-content textarea {
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-family: inherit;
}

.btn-secondary {
  background: #6c757d;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
</style>
