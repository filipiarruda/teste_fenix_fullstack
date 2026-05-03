import axios from 'axios'
import { useAuthStore } from '../stores/auth'

const api = axios.create({
  baseURL: '/api/v1',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

api.interceptors.request.use((config) => {
  const auth = useAuthStore()
  if (auth.token) {
    config.headers.Authorization = `Bearer ${auth.token}`
  }
  return config
})

api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      const auth = useAuthStore()
      auth.logout()
      window.location.href = '/'
    }
    return Promise.reject(error)
  }
)

export default {
  get: api.get.bind(api),
  post: api.post.bind(api),
  put: api.put.bind(api),
  delete: api.delete.bind(api),

  health() {
    return api.get('/health')
  },

  register(data) {
    return api.post('/auth/register', data)
  },

  login(credentials) {
    return api.post('/auth/login', credentials)
  },

  logout() {
    return api.post('/auth/logout')
  },

  getMe() {
    return api.get('/auth/me')
  },

  getExams() {
    return api.get('/exams')
  },

  getExam(id) {
    return api.get(`/exams/${id}`)
  },

  createExam(data) {
    return api.post('/exams', data)
  },

  updateExam(id, data) {
    return api.put(`/exams/${id}`, data)
  },

  deleteExam(id) {
    return api.delete(`/exams/${id}`)
  },

  getQuestions(examId) {
    return api.get(`/exams/${examId}/questions`)
  },

  createQuestion(examId, data) {
    return api.post(`/exams/${examId}/questions`, data)
  },

  updateQuestion(id, data) {
    return api.put(`/questions/${id}`, data)
  },

  deleteQuestion(id) {
    return api.delete(`/questions/${id}`)
  },

  submitExam(data) {
    return api.post('/submissions/submit', data)
  },

  getResult(examId, userId) {
    return api.get(`/submissions/exam/${examId}/result/${userId}`)
  },

  getUserAnswers(examId, userId) {
    return api.get(`/submissions/exam/${examId}/user/${userId}`)
  },

  getExamAnalytics(examId) {
    return api.get(`/analytics/exam/${examId}`)
  },

  getExamRanking(examId) {
    return api.get(`/analytics/exam/${examId}/ranking`)
  },

  getExamAverage(examId) {
    return api.get(`/analytics/exam/${examId}/average`)
  },

  getExamTop(examId) {
    return api.get(`/analytics/exam/${examId}/top`)
  },
}
