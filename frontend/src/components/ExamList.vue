<template>
  <div class="exam-list">
    <h1>Exames Disponíveis</h1>
    <div v-if="loading" class="loading">Carregando...</div>
    <div v-else-if="exams.length === 0" class="no-exams">Nenhum exame disponível</div>
    <ul v-else class="exams">
      <li v-for="exam in exams" :key="exam.id" @click="selectExam(exam)" class="exam-item">
        <h3>{{ exam.title }}</h3>
        <p>{{ exam.description }}</p>
        <span class="questions">{{ exam.total_questions }} questões</span>
        <span class="grade">Nota mínima: {{ exam.passing_grade }}%</span>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import API from '@/services/api'

const exams = ref([])
const loading = ref(true)
const emit = defineEmits(['exam-selected'])

onMounted(async () => {
  try {
    const { data } = await API.getExams()
    exams.value = data
  } catch(e) { 
    console.error('Failed to load exams:', e) 
  }
  finally { 
    loading.value = false 
  }
})

const selectExam = (exam) => emit('exam-selected', exam)
</script>

<style scoped>
.exam-list { 
  padding: 20px; 
}

.exams { 
  list-style: none; 
  padding: 0; 
}

.exam-item { 
  padding: 15px; 
  margin: 10px 0; 
  border: 1px solid #ddd; 
  cursor: pointer; 
  border-radius: 4px; 
  transition: all 0.3s ease;
}

.exam-item:hover { 
  background: #f5f5f5; 
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.exam-item h3 {
  margin: 0 0 10px 0;
  color: #333;
}

.exam-item p {
  margin: 5px 0;
  color: #666;
  font-size: 0.9em;
}

.questions, .grade { 
  display: block; 
  font-size: 0.9em; 
  color: #666; 
  margin-top: 5px; 
}
</style>
