import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(null)
  const isAuthenticated = computed(() => !!token.value && !!user.value)

  const setUser = (userData) => {
    user.value = userData
    localStorage.setItem('auth_user', JSON.stringify(userData))
  }

  const setToken = (authToken) => {
    token.value = authToken
    localStorage.setItem('auth_token', authToken)
    
    // Set default authorization header for axios
    if (window.axios) {
      window.axios.defaults.headers.common['Authorization'] = `Bearer ${authToken}`
    }
  }

  const logout = () => {
    user.value = null
    token.value = null
    localStorage.removeItem('auth_user')
    localStorage.removeItem('auth_token')
    
    // Remove axios authorization header
    if (window.axios) {
      delete window.axios.defaults.headers.common['Authorization']
    }
  }

  const restoreSession = () => {
    const storedUser = localStorage.getItem('auth_user')
    const storedToken = localStorage.getItem('auth_token')
    
    if (storedUser && storedToken) {
      try {
        user.value = JSON.parse(storedUser)
        token.value = storedToken
        
        // Restore axios authorization header
        if (window.axios) {
          window.axios.defaults.headers.common['Authorization'] = `Bearer ${storedToken}`
        }
      } catch (e) {
        logout()
      }
    }
  }

  return {
    user,
    token,
    isAuthenticated,
    setUser,
    setToken,
    logout,
    restoreSession
  }
})
