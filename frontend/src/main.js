import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import { useAuthStore } from './stores/auth'
import api from './services/api'

// Bootstrap
import 'bootstrap/dist/css/bootstrap.min.css'

// Custom dark theme
import './assets/dark-theme.css'

const app = createApp(App)

app.use(createPinia())
app.use(router)

// Make API available globally
app.config.globalProperties.$api = api

// Restore session before routing
const auth = useAuthStore()
auth.restoreSession()

app.mount('#app')
