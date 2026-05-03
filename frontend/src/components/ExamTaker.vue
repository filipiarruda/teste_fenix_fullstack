<template>
  <div class="exam-taker">
    <h1>{{ exam.title }}</h1>
    <p class="progress">Questão {{ currentQuestion + 1 }} de {{ questions.length }}</p>
    <div class="progress-bar">
      <div class="progress-fill" :style="{ width: ((currentQuestion + 1) / questions.length * 100) + '%' }"></div>
    </div>
    
    <div v-if="questions.length > 0" class="question">
      <h2>{{ questions[currentQuestion].content }}</h2>
      <div class="options">
        <label v-for="opt in ['A', 'B', 'C', 'D']" :key="opt" class="option" :class="{ selected: answers[questions[currentQuestion].id] === opt }">
          <input 
            type="radio" 
            :value="opt" 
            v-model="answers[questions[currentQuestion].id]" 
          />
          <span class="option-label">{{ opt }}: {{ questions[currentQuestion][`option_${opt.toLowerCase()}`] }}</span>
        </label>
      </div>
    </div>
    
    <div class="controls">
      <button v-if="currentQuestion > 0" @click="previousQuestion" class="btn-nav">← Anterior</button>
      <button v-if="currentQuestion < questions.length - 1" @click="nextQuestion" class="btn-nav">Próxima →</button>
      <button v-else @click="submitExam" class="btn-submit">Enviar Prova</button>
    </div>
  </div>
</template>

<script setup>
import { ref, defineProps } from 'vue'
import API from '@/services/api'

const props = defineProps({
  exam: Object,
  questions: Array
})

const currentQuestion = ref(0)
const answers = ref({})
const emit = defineEmits(['exam-completed'])

const nextQuestion = () => { 
  if(currentQuestion.value < props.questions.length - 1) 
    currentQuestion.value++ 
}

const previousQuestion = () => { 
  if(currentQuestion.value > 0) 
    currentQuestion.value-- 
}

const submitExam = async () => {
  try {
    const result = await API.submitAnswers(props.exam.id, answers.value)
    emit('exam-completed', result)
  } catch(e) { 
    console.error('Failed to submit exam:', e) 
  }
}
</script>

<style scoped>
.exam-taker { 
  padding: 20px; 
}

.progress { 
  color: #666; 
  font-size: 0.9em; 
  margin-bottom: 10px;
}

.progress-bar {
  width: 100%;
  height: 8px;
  background: #eee;
  border-radius: 4px;
  overflow: hidden;
  margin-bottom: 20px;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #667eea, #764ba2);
  transition: width 0.3s ease;
}

.question { 
  margin: 20px 0; 
  padding: 20px; 
  border: 1px solid #ddd; 
  border-radius: 4px; 
  background: #fafafa;
}

.question h2 {
  margin: 0 0 20px 0;
  color: #333;
  font-size: 1.2em;
}

.options { 
  margin: 15px 0; 
}

.option { 
  display: flex;
  align-items: center;
  padding: 12px; 
  margin: 8px 0; 
  border: 2px solid #eee; 
  border-radius: 4px; 
  cursor: pointer;
  transition: all 0.3s ease;
}

.option:hover { 
  background: #f5f5f5;
  border-color: #ddd;
}

.option.selected {
  background: #e3f2fd;
  border-color: #667eea;
}

.option input[type="radio"] {
  margin-right: 10px;
  cursor: pointer;
}

.option-label {
  cursor: pointer;
  flex: 1;
}

.controls { 
  margin-top: 30px;
  display: flex;
  gap: 10px;
  justify-content: center;
}

.btn-nav, .btn-submit { 
  padding: 12px 24px; 
  cursor: pointer; 
  border: none; 
  border-radius: 4px; 
  font-size: 1em;
  transition: all 0.3s ease;
}

.btn-nav {
  background: #667eea;
  color: white;
}

.btn-nav:hover {
  background: #764ba2;
  transform: translateY(-2px);
}

.btn-submit { 
  background: linear-gradient(135deg, #28a745, #20c997);
  color: white;
  font-weight: bold;
  min-width: 150px;
}

.btn-submit:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}
</style>
