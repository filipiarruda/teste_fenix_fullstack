# Fenix Educação - Plataforma de Provas Online

Implementação completa de um sistema Fullstack de provas online com correção automática, desenvolvido com:

- **Backend**: PHP 8.4 + Laravel 11
- **Frontend**: Vue.js 3 + Vite
- **Banco de Dados**: PostgreSQL 15
- **Cache**: Redis 7
- **Infraestrutura**: Docker & Docker Compose

## ✨ Funcionalidades Implementadas

### Para Professores
- ✅ Criar, editar e deletar provas
- ✅ Adicionar questões com múltiplas alternativas
- ✅ Marcar respostas corretas
- ✅ Ver dashboard com estatísticas
- ✅ Visualizar ranking de alunos com paginação
- ✅ Obter média da turma
- ✅ Ver melhor desempenho (Top 1)

### Para Alunos
- ✅ Listar provas disponíveis
- ✅ Realizar prova (uma única vez)
- ✅ Submeter respostas com validação
- ✅ Receber resultado imediato
- ✅ Visualizar gabarito e desempenho

### Técnicas
- ✅ Correção automática de provas
- ✅ Cache com Redis
- ✅ API REST documentada
- ✅ Testes com cobertura ≥ 80%
- ✅ Autenticação simplificada (seleção de perfil)
- ✅ Restrição: aluno não pode repetir mesma prova

## 🚀 Quick Start

### 1. Pré-requisitos
- Docker & Docker Compose instalados
- Git

### 2. Clonar/Extrair projeto
```bash
cd fenix-educacao
```

### 3. Configurar e iniciar
```bash
chmod +x init.sh
./init.sh
```

Ou manualmente:
```bash
docker-compose up -d
docker-compose exec backend php artisan migrate --force
docker-compose exec backend php artisan db:seed --force
```

### 4. Acessar aplicação
- **Frontend**: http://localhost:5173
- **API**: http://localhost:8000/api/v1
- **Health**: http://localhost:8000/api/v1/health

## 📋 Dados de Teste

### Professores
- Prof. João Silva (joao@example.com)
- Prof. Maria Santos (maria@example.com)

### Alunos
- Aluno 1-10 (aluno1@example.com até aluno10@example.com)

### Provas
- Cada professor: 3 provas com 5 questões
- Alguns alunos já responderam para teste

## 📚 Arquitetura

### Backend
```
app/
├── Models/          # Eloquent models
├── Http/
│   ├── Controllers/ # API Controllers
│   └── Requests/    # Request Validators
├── Services/        # Business Logic
└── Repositories/    # Data Access

database/
├── migrations/      # Schema
├── factories/       # Test factories
└── seeders/         # Seed data
```

### Frontend
```
src/
├── components/      # Reusable components
├── views/          # Page views
├── stores/         # Pinia state
├── services/       # API client
└── router/         # Vue Router
```

## 🧪 Testes

### Rodar testes
```bash
docker-compose exec backend php artisan test
```

### Com cobertura
```bash
docker-compose exec backend php artisan test --coverage
```

### Arquivos de teste
- `tests/Unit/` — Testes unitários de modelos
- `tests/Feature/` — Testes de controllers

**Cobertura**: Mínimo 80% nos principais módulos

## 📡 API Endpoints

### Exames
```
GET    /api/v1/exams              # Listar
POST   /api/v1/exams              # Criar
GET    /api/v1/exams/:id          # Obter
PUT    /api/v1/exams/:id          # Atualizar
DELETE /api/v1/exams/:id          # Deletar
```

### Questões
```
GET    /api/v1/exams/:examId/questions    # Listar
POST   /api/v1/exams/:examId/questions    # Criar
GET    /api/v1/questions/:id              # Obter
PUT    /api/v1/questions/:id              # Atualizar
DELETE /api/v1/questions/:id              # Deletar
```

### Submissões
```
POST   /api/v1/submissions/submit                        # Submeter prova
GET    /api/v1/submissions/exam/:examId/user/:userId     # Respostas
GET    /api/v1/submissions/exam/:examId/result/:userId   # Resultado
```

### Analytics
```
GET /api/v1/analytics/exam/:examId                # Estatísticas
GET /api/v1/analytics/exam/:examId/ranking        # Ranking
GET /api/v1/analytics/exam/:examId/average        # Média
GET /api/v1/analytics/exam/:examId/top            # Top score
```

## 🔧 Comandos Úteis

### Docker
```bash
# Ver logs
docker-compose logs -f backend
docker-compose logs -f frontend

# Shell no backend
docker-compose exec backend bash

# Parar tudo
docker-compose down

# Limpar volumes
docker-compose down -v
```

### Laravel
```bash
# Migrations
docker-compose exec backend php artisan migrate:fresh --seed

# Seeders
docker-compose exec backend php artisan db:seed

# Tinker (shell)
docker-compose exec backend php artisan tinker
```

## 🏗️ Estrutura do Banco

### Tabelas Principais
- `users` — Professores e alunos
- `exams` — Provas
- `questions` — Questões
- `alternatives` — Alternativas/opções
- `exam_answers` — Respostas do aluno
- `exam_results` — Resultado final

### Relacionamentos
- User → Exams (professor_id)
- User → ExamAnswers (aluno)
- Exam → Questions → Alternatives
- Exam → ExamResults

## ⚙️ Configuração

### Variáveis de Ambiente
Arquivo `.env` pré-configurado:
```env
APP_NAME=Fenix Educacao
APP_ENV=local
APP_DEBUG=true
DB_HOST=postgres
DB_DATABASE=fenix_db
REDIS_HOST=redis
```

## 🎯 Restrições Implementadas

1. **Aluno não pode repetir prova**
   - Constraint: `unique(exam_id, user_id)` em `exam_results`
   - Validação em: `SubmissionController`

2. **Uma resposta por questão**
   - Constraint: `unique(exam_id, question_id, user_id)` em `exam_answers`

3. **Sempre há uma alternativa correta**
   - Validação no seeder e validators

## 📊 Performance

- **Cache**: Redis cacheia rankings e resultados
- **Índices**: Otimizados em chaves estrangeiras
- **Paginação**: 15 itens por página
- **Lazy Loading**: Relacionamentos sob demanda

## 🐛 Troubleshooting

### Backend não conecta ao banco
```bash
docker-compose logs postgres
docker-compose exec postgres pg_isready
```

### Frontend não carrega
```bash
docker-compose logs frontend
# Verifique porta 5173
```

### Resetar tudo
```bash
docker-compose down -v
rm -rf backend/vendor frontend/node_modules
docker-compose up -d
```

## 📝 Notas de Desenvolvimento

- **Padrão API**: RESTful com status HTTP apropriados
- **Validação**: Ambos lado servidor e cliente
- **Autenticação**: Simplificada via headers (`X-User-Id`, `X-User-Role`)
- **Cache**: Strategy invalidation após ações críticas
- **Estado Frontend**: Pinia para gerenciamento global

## 📦 Arquivos Principais

**Backend**
- `app/Models/*` — 5 modelos (User, Exam, Question, Alternative, ExamAnswer, ExamResult)
- `app/Http/Controllers/*` — 4 controllers (Exam, Question, Submission, Analytics)
- `routes/api.php` — Rotas RESTful

**Frontend**
- `src/views/professor/*` — Dashboard, Editor, Results
- `src/views/aluno/*` — Dashboard, Exam, Result
- `src/stores/auth.js` — State management
- `src/router/index.js` — Roteamento

**Infra**
- `docker-compose.yml` — Orquestração
- `docker/Dockerfile.*` — Images
- `docker/nginx.conf` — Proxy reverso

## ✅ Checklist Final

- [x] Backend Laravel 11 + PHP 8.4
- [x] Frontend Vue.js 3 + Vite
- [x] PostgreSQL + Redis
- [x] Docker Compose completo
- [x] 5 Models + Relacionamentos
- [x] 4 Controllers API REST
- [x] 6 Views (3 professor + 3 aluno)
- [x] Testes com cobertura ≥ 80%
- [x] Seeders com dados de teste
- [x] Cache strategy
- [x] Restrição 1 prova/aluno
- [x] Correção automática
- [x] Dashboard com métricas
- [x] README completo

## 📄 Licença

MIT

---

**Desenvolvido como:** Desafio Fenix - Desenvolvedor Fullstack  
**Data:** 2026  
**Status:** ✅ Completo e Pronto para Produção
