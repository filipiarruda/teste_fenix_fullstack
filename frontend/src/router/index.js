import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  {
    path: '/',
    name: 'login',
    component: () => import('@/views/LoginView.vue'),
    meta: { guest: true },
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('@/views/RegisterView.vue'),
    meta: { guest: true },
  },
  {
    path: '/professor',
    name: 'professor-dashboard',
    component: () => import('@/views/ProfessorDashboard.vue'),
    meta: { requiresAuth: true, role: 'professor' },
  },
  {
    path: '/professor/exams',
    name: 'exam-list',
    component: () => import('@/views/professor/ExamListView.vue'),
    meta: { requiresAuth: true, role: 'professor' },
  },
  {
    path: '/professor/exams/new',
    name: 'exam-create',
    component: () => import('@/views/professor/ExamFormView.vue'),
    meta: { requiresAuth: true, role: 'professor' },
  },
  {
    path: '/professor/exams/:id/edit',
    name: 'exam-edit',
    component: () => import('@/views/professor/ExamFormView.vue'),
    meta: { requiresAuth: true, role: 'professor' },
  },
  {
    path: '/aluno',
    name: 'student-dashboard',
    component: () => import('@/views/StudentDashboard.vue'),
    meta: { requiresAuth: true, role: 'student' },
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to) => {
  const auth = useAuthStore()

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'login' }
  }

  if (to.meta.role && auth.user?.role !== to.meta.role) {
    // Redirect to correct dashboard if wrong role
    if (auth.isAuthenticated) {
      return auth.user.role === 'professor'
        ? { name: 'professor-dashboard' }
        : { name: 'student-dashboard' }
    }
    return { name: 'login' }
  }

  if (to.meta.guest && auth.isAuthenticated) {
    return auth.user.role === 'professor'
      ? { name: 'professor-dashboard' }
      : { name: 'student-dashboard' }
  }
})

export default router
