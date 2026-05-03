<template>
  <div class="student-dashboard">
    <h1>Meu Dashboard - Estudante</h1>
    
    <div class="stats-row">
      <div class="stat">
        <h3>{{ completedExams }}</h3>
        <p>Provas Feitas</p>
      </div>
      <div class="stat">
        <h3>{{ passedExams }}</h3>
        <p>Aprovações</p>
      </div>
      <div class="stat">
        <h3>{{ averageScore.toFixed(1) }}%</h3>
        <p>Média</p>
      </div>
    </div>

    <div class="results-section">
      <h2>Histórico de Resultados</h2>
      <div v-if="results.length === 0" class="no-results">Nenhum resultado ainda</div>
      <div v-else class="results-grid">
        <div v-for="result in results" :key="result.id" class="result-card" :class="{ passed: result.passed }">
          <h3>{{ result.exam_title }}</h3>
          <p class="score">{{ result.score.toFixed(1) }}%</p>
          <p class="correct">{{ result.correct_answers }}/{{ result.total_questions }} corretas</p>
          <p class="status" :class="{ 'status-passed': result.passed, 'status-failed': !result.passed }">
            {{ result.passed ? '✓ Aprovado' : '✗ Reprovado' }}
          </p>
        </div>
      </div>
    </div>

    <div class="available-exams">
      <h2>Provas Disponíveis</h2>
      <div v-if="availableExams.length === 0" class="no-exams">Nenhuma prova disponível</div>
      <ul v-else class="exams-list">
        <li v-for="exam in availableExams" :key="exam.id" class="exam-item">
          <div class="exam-info">
            <h3>{{ exam.title }}</h3>
            <p>{{ exam.description }}</p>
            <span class="exam-meta">{{ exam.total_questions }} questões | Nota mínima: {{ exam.passing_grade }}%</span>
          </div>
          <button @click="startExam(exam)" class="btn-start">Fazer Prova</button>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import API from '@/services/api'

const completedExams = ref(0)
const passedExams = ref(0)
const averageScore = ref(0)
const results = ref([])
const availableExams = ref([])

const emit = defineEmits(['exam-selected'])

onMounted(async () => {
  try {
    // Load available exams
    const { data: exams } = await API.getExams()
    availableExams.value = exams

    // Mock results data
    results.value = [
      {
        id: 1,
        exam_title: 'Prova de Matemática',
        score: 85,
        correct_answers: 4,
        total_questions: 5,
        passed: true
      },
      {
        id: 2,
        exam_title: 'Prova de Português',
        score: 60,
        correct_answers: 3,
        total_questions: 5,
        passed: true
      }
    ]

    completedExams.value = results.value.length
    passedExams.value = results.value.filter(r => r.passed).length
    averageScore.value = results.value.reduce((sum, r) => sum + r.score, 0) / results.value.length
  } catch(e) {
    console.error('Failed to load data:', e)
  }
})

const startExam = (exam) => {
  emit('exam-selected', exam)
}
</script>

<style scoped>
.student-dashboard {
  padding: 20px;
}

.stats-row {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 15px;
  margin: 20px 0;
}

.stat {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  padding: 20px;
  border-radius: 8px;
  text-align: center;
}

.stat h3 {
  font-size: 2.5em;
  margin: 0;
  font-weight: bold;
}

.stat p {
  margin: 10px 0 0 0;
  font-size: 0.95em;
  opacity: 0.9;
}

.results-section {
  margin: 30px 0;
}

.results-section h2 {
  margin-bottom: 20px;
  color: #333;
}

.results-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 15px;
}

.result-card {
  padding: 20px;
  border-radius: 8px;
  background: #f9f9f9;
  border-left: 4px solid #dc3545;
}

.result-card.passed {
  border-left-color: #28a745;
}

.result-card h3 {
  margin: 0 0 10px 0;
  color: #333;
}

.score {
  font-size: 1.8em;
  font-weight: bold;
  color: #667eea;
  margin: 10px 0;
}

.correct {
  color: #666;
  font-size: 0.9em;
}

.status {
  margin-top: 10px;
  font-weight: 600;
}

.status-passed {
  color: #28a745;
}

.status-failed {
  color: #dc3545;
}

.available-exams {
  margin-top: 40px;
}

.available-exams h2 {
  margin-bottom: 20px;
  color: #333;
}

.exams-list {
  list-style: none;
  padding: 0;
}

.exam-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px;
  border: 1px solid #ddd;
  border-radius: 8px;
  margin-bottom: 10px;
  gap: 15px;
}

.exam-info {
  flex: 1;
}

.exam-info h3 {
  margin: 0 0 5px 0;
  color: #333;
}

.exam-info p {
  margin: 5px 0;
  color: #666;
  font-size: 0.9em;
}

.exam-meta {
  display: block;
  color: #999;
  font-size: 0.85em;
  margin-top: 8px;
}

.btn-start {
  padding: 10px 20px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 500;
  white-space: nowrap;
  transition: all 0.3s ease;
}

.btn-start:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.no-results, .no-exams {
  text-align: center;
  padding: 40px 20px;
  color: #999;
  font-size: 1.1em;
}
</style>
