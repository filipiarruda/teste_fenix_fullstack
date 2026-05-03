import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: () => import('../views/LoginView.vue'),
    meta: { requiresGuest: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('../views/RegisterView.vue'),
    meta: { requiresGuest: true }
  },
  {
    path: '/professor',
    name: 'ProfessorDashboard',
    component: () => import('../views/professor/DashboardView.vue'),
    meta: { requiresAuth: true, role: 'professor' },
  },
  {
    path: '/professor/exam/create',
    name: 'CreateExam',
    component: () => import('../views/professor/ExamEditorView.vue'),
    meta: { requiresAuth: true, role: 'professor' },
  },
  {
    path: '/professor/exam/:id/edit',
    name: 'EditExam',
    component: () => import('../views/professor/ExamEditorView.vue'),
    meta: { requiresAuth: true, role: 'professor' },
  },
  {
    path: '/professor/exam/:id/results',
    name: 'ExamResults',
    component: () => import('../views/professor/ExamResultsView.vue'),
    meta: { requiresAuth: true, role: 'professor' },
  },
  {
    path: '/student',
    name: 'StudentDashboard',
    component: () => import('../views/aluno/DashboardView.vue'),
    meta: { requiresAuth: true, role: 'student' },
  },
  {
    path: '/student/exam/:id',
    name: 'TakeExam',
    component: () => import('../views/aluno/ExamView.vue'),
    meta: { requiresAuth: true, role: 'student' },
  },
  {
    path: '/student/exam/:id/result',
    name: 'ExamResultStudent',
    component: () => import('../views/aluno/ResultView.vue'),
    meta: { requiresAuth: true, role: 'student' },
  },
  // Redirect legacy /aluno to /student
  {
    path: '/aluno',
    redirect: '/student'
  },
  {
    path: '/aluno/:pathMatch(.*)*',
    redirect: '/student'
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  const auth = useAuthStore()
  const isAuthenticated = auth.isAuthenticated
  const userRole = auth.user?.role

  // Redirect authenticated users away from login/register
  if (to.meta.requiresGuest && isAuthenticated) {
    next(userRole === 'professor' ? '/professor' : '/student')
    return
  }

  // Check if route requires authentication
  if (to.meta.requiresAuth) {
    if (!isAuthenticated) {
      next('/')
    } else if (to.meta.role && to.meta.role !== userRole) {
      // User doesn't have permission for this role
      next(userRole === 'professor' ? '/professor' : '/student')
    } else {
      next()
    }
  } else {
    next()
  }
})

export default router
