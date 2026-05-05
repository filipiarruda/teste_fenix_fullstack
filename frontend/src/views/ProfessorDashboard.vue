<template>
  <div class="min-vh-100" style="background: var(--bg-primary); color: var(--text-primary);">
    <nav class="navbar navbar-dark px-4 py-3" style="background: var(--bg-card); border-bottom: 1px solid var(--bs-border-color);">
      <span class="navbar-brand fw-bold">Fenix Educação — Professor</span>
      <button class="btn btn-sm btn-outline-secondary" @click="handleLogout">Sair</button>
    </nav>
    <div class="container py-5">
      <h2 class="mb-1">Olá, {{ auth.user?.name }}</h2>
      <p class="text-muted">Painel do professor — em construção.</p>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()

async function handleLogout() {
  await auth.logout()
  router.push('/')
}
</script>
