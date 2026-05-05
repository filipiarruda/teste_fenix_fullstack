<template>
  <div class="container py-5">
    <div class="row">
      <div class="col-lg-8">
        <div class="mb-5">
          <h1 class="mb-2">Bem-vindo, {{ auth.user?.name }}!</h1>
          <p class="text-muted">Gerencie suas provas e acompanhe o desempenho dos alunos.</p>
        </div>

        <!-- Cards de Ações Rápidas -->
        <div class="row g-4 mb-5">
          <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm text-center">
              <div class="card-body py-5">
                <div class="mb-3 text-primary" style="font-size: 2.5rem;">
                  📋
                </div>
                <h5 class="card-title">Minhas Provas</h5>
                <p class="card-text text-muted">Crie, edite e gerencie suas provas</p>
                <RouterLink to="/professor/exams" class="btn btn-primary btn-sm">
                  Ver Provas
                </RouterLink>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm text-center">
              <div class="card-body py-5">
                <div class="mb-3 text-success" style="font-size: 2.5rem;">
                  ➕
                </div>
                <h5 class="card-title">Nova Prova</h5>
                <p class="card-text text-muted">Comece a criar uma nova prova</p>
                <RouterLink to="/professor/exams/new" class="btn btn-success btn-sm">
                  Criar
                </RouterLink>
              </div>
            </div>
          </div>
        </div>

        <!-- Informações -->
        <div class="alert alert-info">
          <h6 class="alert-heading">💡 Dica</h6>
          <p class="mb-0">
            Crie provas com múltiplas questões e opções. Os alunos poderão respondê-las e receberão 
            feedback instantâneo com o resultado e o gabarito correto.
          </p>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-light">
            <h6 class="mb-0">Seu Perfil</h6>
          </div>
          <div class="card-body">
            <p class="mb-2">
              <strong>Nome:</strong><br/>
              {{ auth.user?.name }}
            </p>
            <p class="mb-2">
              <strong>Email:</strong><br/>
              {{ auth.user?.email }}
            </p>
            <p class="mb-0">
              <strong>Cargo:</strong><br/>
              <span class="badge bg-primary">Professor</span>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { RouterLink } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
</script>
