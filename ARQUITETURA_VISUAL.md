# 🏗️ ARQUITETURA VISUAL - FENIX EDUCAÇÃO

## Sistema Completo - Visão Geral

```
┌─────────────────────────────────────────────────────────────────────┐
│                        INTERNET / USUÁRIOS                          │
└──────────────┬──────────────────────────────────────┬────────────────┘
               │                                      │
         ┌─────▼─────┐                          ┌─────▼─────┐
         │  Professor │                          │   Aluno   │
         │ (navegador)│                          │(navegador)│
         └─────┬─────┘                          └─────┬─────┘
               │ HTTP/HTTPS                           │ HTTP/HTTPS
               └──────────────┬──────────────────────┘
                              │
        ┌─────────────────────▼─────────────────────┐
        │          NGINX - Reverse Proxy             │
        │     (balanceador + cache de assets)       │
        ├──────────────────┬──────────────────────┤
        │                  │                      │
┌───────▼────────┐    ┌────▼──────────┐   ┌──────▼────────┐
│  Frontend      │    │   Backend      │   │  Banco + Cache│
│  Vue.js 3      │    │   Laravel 11   │   │               │
│  (porta 5173)  │    │   (porta 8000) │   │               │
└────────────────┘    └────────┬───────┘   └──────────────┘
     │                         │
     │                    ┌────▼──────┐
     └───────────────────►│  API REST  │
                          └────┬───────┘
                               │
                    ┌──────────┼──────────┐
                    │          │         │
              ┌─────▼──┐  ┌────▼───┐  ┌─▼────────┐
              │ Exams  │  │ Submit  │  │Analytics │
              │ CRUD   │  │ Result  │  │ Ranking  │
              └────────┘  └────────┘  └──────────┘
                               │
                         ┌─────▼──────┐
                         │ PostgreSQL  │
                         │   Port 5432 │
                         └─────────────┘
                               │
                         ┌─────▼──────┐
                         │   Redis    │
                         │  Port 6379 │
                         └────────────┘
```

---

## Stack Técnico - Camadas

```
┌──────────────────────────────────────────────────────────────┐
│                     BROWSER / CLIENT                         │
│  ┌────────────────────────────────────────────────────────┐  │
│  │             Frontend - Vue.js 3 + Vite                 │  │
│  │  ┌──────────────────────────────────────────────────┐  │  │
│  │  │ Views (7):                                       │  │  │
│  │  │ • LoginView (seleção de perfil)                 │  │  │
│  │  │ • Professor: Dashboard, Editor, Results         │  │  │
│  │  │ • Aluno: Dashboard, Exam, Result                │  │  │
│  │  └──────────────────────────────────────────────────┘  │  │
│  │  ┌──────────────────────────────────────────────────┐  │  │
│  │  │ Router: vue-router 4                             │  │  │
│  │  │ State: Pinia (auth, exam stores)                │  │  │
│  │  │ HTTP: Axios (com interceptors)                  │  │  │
│  │  └──────────────────────────────────────────────────┘  │  │
│  └────────────────────────────────────────────────────────┘  │
└────────────────────┬─────────────────────────────────────────┘
                     │
                     │ HTTP REST API
                     │
┌────────────────────▼─────────────────────────────────────────┐
│                 NGINX REVERSE PROXY                          │
│  • Balanceador de carga                                      │
│  • Cache de assets estáticos                                │
│  • Compressão (gzip)                                         │
│  • CORS configurado                                          │
└────────────────────┬─────────────────────────────────────────┘
                     │
        ┌────────────▼────────────┐
        │                         │
┌───────▼──────────────┐   ┌──────▼──────────────┐
│    BACKEND - PHP 8.4 │   │   ORM + CACHE      │
│    Laravel 11        │   │                    │
│                      │   │ • Eloquent ORM     │
│ ┌──────────────────┐ │   │ • Redis Cache      │
│ │  CONTROLLERS (4) │ │   │ • Query Builder    │
│ ├──────────────────┤ │   └────────────────────┘
│ │ • ExamCtrl       │ │
│ │ • QuestionCtrl   │ │   ┌────────────────────┐
│ │ • SubmissionCtrl │ │   │ SERVICE LAYER      │
│ │ • AnalyticsCtrl  │ │   │                    │
│ └────────────┬──────┘ │   │ • ExamService      │
│              │        │   │ • Submission Svc   │
│ ┌────────────▼──────┐ │   │ • Analytics Svc    │
│ │ SERVICES (3)      │ │   └────────────────────┘
│ ├──────────────────┐ │
│ │ Business Logic   │ │   ┌────────────────────┐
│ └────────────┬─────┘ │   │ REPOSITORIES       │
│              │       │   │                    │
│ ┌────────────▼──────┐ │   │ • ExamRepository   │
│ │ REPOSITORIES (3)  │ │   │ • QuestionRepo     │
│ ├──────────────────┐ │   │ • SubmissionRepo   │
│ │ Data Access      │ │   └────────────────────┘
│ └────────────┬─────┘ │
│              │       │   ┌────────────────────┐
│ ┌────────────▼──────┐ │   │ MODELS (6)         │
│ │ MODELS (6)        │ │   │                    │
│ ├──────────────────┐ │   │ • User             │
│ │ • User           │ │   │ • Exam             │
│ │ • Exam           │ │   │ • Question         │
│ │ • Question       │ │   │ • Alternative      │
│ │ • Alternative    │ │   │ • ExamAnswer       │
│ │ • ExamAnswer     │ │   │ • ExamResult       │
│ │ • ExamResult     │ │   └────────────────────┘
│ └──────────────────┘ │
│                      │
│ ┌──────────────────┐ │   ┌────────────────────┐
│ │ ROUTES - API REST│ │   │ REQUEST VALIDATORS │
│ ├──────────────────┤ │   │                    │
│ │ /api/v1/exams   │ │   │ • StoreExamReq     │
│ │ /api/v1/qs      │ │   │ • UpdateExamReq    │
│ │ /api/v1/submit  │ │   │ • StoreQuestionReq │
│ │ /api/v1/stats   │ │   │ • SubmitExamReq    │
│ └──────────────────┘ │   └────────────────────┘
└──────────────┬───────┘
               │
        ┌──────┴──────┐
        │             │
┌───────▼──────┐  ┌───▼──────────┐
│ PostgreSQL   │  │ Redis Cache  │
│ Port 5432    │  │ Port 6379    │
│              │  │              │
│ • users      │  │ • exam:*     │
│ • exams      │  │ • ranking:*  │
│ • questions  │  │ • result:*   │
│ • altern.    │  │              │
│ • answers    │  └──────────────┘
│ • results    │
└──────────────┘
```

---

## Fluxo de Dados - Criar e Responder Prova

### 📝 Fluxo Professor - Criar Prova

```
Professor (Vue.js)
      │
      │ 1. Preenche formulário
      ├─► ExamEditorView.vue
      │   • title, description
      │   • questions[] com alternatives[]
      │
      │ 2. Clica "Salvar"
      ├─► examService.createExam()
      │
      │ 3. Envia POST /api/v1/exams
      ├─► Axios HTTP
      │
      │ 4. Backend recebe
      ├─► ExamController.store()
      │   • StoreExamRequest (validação)
      │   • ExamService.createWithQuestions()
      │
      │ 5. Salva em banco
      ├─► Exam::create()
      ├─► Question::create() (loop)
      ├─► Alternative::create() (loop)
      │
      │ 6. Retorna response JSON
      ├─► HTTP 201 Created
      │
      │ 7. Frontend atualiza UI
      └─► DashboardView.vue lista nova prova
```

### ✏️ Fluxo Aluno - Responder Prova

```
Aluno (Vue.js)
      │
      │ 1. Clica "Responder" em prova
      ├─► ExamView.vue
      │   • Carrega questions + alternatives
      │   • Mostra questão 1 de N
      │
      │ 2. Seleciona alternative para cada questão
      ├─► Armazena em local state (Pinia)
      │
      │ 3. Clica "Enviar" na última questão
      ├─► Confirmação via modal
      │
      │ 4. Envia POST /api/v1/submissions/submit
      ├─► Axios com payload:
      │   {
      │     exam_id,
      │     answers: [
      │       { question_id, alternative_id },
      │       ...
      │     ]
      │   }
      │
      │ 5. Backend processa
      ├─► SubmissionController.submit()
      │   • Validações (prova não repetível)
      │   • ExamAnswer::create() para cada resposta
      │   • Calcula score
      │   • ExamResult::create()
      │
      │ 6. Verifica se já respondeu
      ├─► Se sim: retorna 422 Conflict
      ├─► Se não: continua
      │
      │ 7. Calcula resultado
      ├─► Itera alternatives corretas
      ├─► Compara com respostas do aluno
      ├─► Calcula percentage
      │
      │ 8. Retorna response com resultado
      ├─► HTTP 200 + resultado JSON
      │
      │ 9. Frontend exibe resultado
      └─► ResultView.vue
          • Percentual em grande
          • Acertos/Total
          • Gabarito detalhado
```

---

## Modelo de Dados - Relacionamentos

```
┌──────────────┐
│    USERS     │
├──────────────┤
│ id (PK)      │
│ name         │          ┌─────────────┐
│ email        ├─────────►│    EXAMS    │
│ role enum    │ 1:N      ├─────────────┤
│ created_at   │          │ id (PK)     │
│ updated_at   │          │ prof_id (FK)│◄─┐
└──────────────┘          │ title       │  │
                          │ description │  │ 1:1
                          │ created_at  │  │
                          └──────┬──────┘  │
                                 │         │
                            1:N  │         │
                                 │         │
        ┌────────────────────────┴─────────┤
        │                                  │
┌───────▼──────────────┐      ┌────────────▼────┐
│    QUESTIONS         │      │  EXAM_RESULTS   │
├──────────────────────┤      ├─────────────────┤
│ id (PK)              │      │ id (PK)         │
│ exam_id (FK)         │      │ exam_id (FK)    │
│ text                 │      │ user_id (FK)    │
│ order                │      │ total_questions │
│ created_at           │      │ correct_answers │
│ updated_at           │      │ score           │
└──────────┬───────────┘      │ percentage      │
           │                  │ created_at      │
      1:N  │                  └─────────────────┘
           │
           │          UNIQUE(exam_id, user_id)
           │          ↳ Previne 2ª submissão
           │
    ┌──────▼────────────────┐
    │   ALTERNATIVES        │
    ├───────────────────────┤
    │ id (PK)               │
    │ question_id (FK)      │
    │ text                  │
    │ is_correct (boolean)  │  ◄─ Marca resposta certa
    │ order                 │
    │ created_at            │
    └───────────┬───────────┘
                │
            N:1 │
                │
        ┌───────▼────────────────┐
        │    EXAM_ANSWERS        │
        ├────────────────────────┤
        │ id (PK)                │
        │ exam_id (FK)           │
        │ question_id (FK)       │
        │ user_id (FK)           │
        │ alternative_id (FK)    │  ◄─ Resposta do aluno
        │ created_at             │
        └────────────────────────┘
        
        UNIQUE(exam_id, question_id, user_id)
        ↳ Uma resposta por questão por aluno
```

---

## Endpoints API - Estrutura

```
BASE: http://localhost:8000/api/v1

EXAMS
├─ GET    /exams                  → Listar (todos ou do professor)
├─ POST   /exams                  → Criar prova
├─ GET    /exams/{id}             → Obter uma prova
├─ PUT    /exams/{id}             → Atualizar prova
└─ DELETE /exams/{id}             → Deletar prova

QUESTIONS
├─ GET    /exams/{examId}/questions       → Listar questões
├─ POST   /exams/{examId}/questions       → Criar questão
├─ GET    /questions/{id}                 → Obter questão
├─ PUT    /questions/{id}                 → Atualizar questão
└─ DELETE /questions/{id}                 → Deletar questão

SUBMISSIONS
├─ POST   /submissions/submit                  → Submeter prova
├─ GET    /submissions/exam/{examId}/user/{userId}  → Respostas
└─ GET    /submissions/exam/{examId}/result/{userId} → Resultado

ANALYTICS
├─ GET    /analytics/exam/{examId}             → Stats gerais
├─ GET    /analytics/exam/{examId}/ranking     → Ranking paginado
├─ GET    /analytics/exam/{examId}/average     → Média turma
└─ GET    /analytics/exam/{examId}/top         → Top 1 score

HEALTH
└─ GET    /health                 → Status da API
```

---

## Fluxo de Autenticação - Simplificado

```
┌─────────────┐
│  Frontend   │
└──────┬──────┘
       │
       │ 1. User seleciona role
       ├─► LoginView.vue
       │
       │ 2. User seleciona de lista
       ├─► Dados mock (2 profs + 10 alunos)
       │
       │ 3. Armazena localStorage
       ├─► user = { id, name, role }
       │
       │ 4. Todos os requests incluem headers
       ├─► X-User-Id: "123"
       ├─► X-User-Role: "professor"
       │
       │ 5. Backend recebe headers
       └─► Controllers validam role
           • Professor: dados pessoais
           • Aluno: dados pessoais + restrições
```

---

## Ciclo de Vida - Prova do Início ao Fim

```
T0: Professor cria prova
└─ ExamEditor → POST /exams → Exam created

T1: Seeders populam dados
└─ Alunos criados, prova existente

T2: Aluno acessa dashboard
├─ GET /exams → Vê prova disponível
└─ Status: "Respondida" = falso (verde/amarelo)

T3: Aluno clica "Responder"
└─ ExamView carrega:
   ├─ GET /exams/{id}
   └─ Mostra questões + alternativas

T4: Aluno responde questões
└─ State local armazena respostas

T5: Aluno submete
├─ POST /submissions/submit
├─ Backend calcula score
├─ ExamResult criado
└─ HTTP 200 + resultado

T6: Resultado exibido
└─ ResultView mostra:
   ├─ Percentual
   ├─ Acertos/Total
   └─ Gabarito detalhado

T7: Aluno tenta responder novamente
├─ GET /exams → Status agora "Respondida"
├─ Clica responder → vê resultado anterior
└─ POST /submissions/submit → 422 Conflict

T8: Professor vê resultados
├─ GET /analytics/exam/{id}
├─ Ver ranking com AnalyticsController
└─ Paginação (15 itens/página)
```

---

## Stack de Deploy - Docker

```
┌──────────────────────────────────────────────────────┐
│                  Docker Compose                      │
│  Orquestra 5 serviços em containers                 │
└──────────────┬───────────────────────────────────────┘
               │
        ┌──────┴──────────┬────────────┬───────────┐
        │                 │            │           │
┌───────▼────────┐  ┌─────▼────┐  ┌───▼────┐  ┌──▼─────┐
│  PostgreSQL    │  │  Redis   │  │Backend │  │Frontend│
│  Port 5432     │  │ 6379     │  │8000    │  │5173    │
│  Volume: db/   │  │ Cache    │  │Laravel │  │Vue.js  │
│  Network: app  │  │ Network: │  │Fpm     │  │Vite    │
│                │  │ app      │  │Node.js │  │Node.js │
└────────────────┘  └──────────┘  └────┬───┘  └──┬─────┘
                                       │        │
                            ┌──────────┴───┬────┘
                            │              │
                       ┌────▼──────────────▼────┐
                       │      NGINX             │
                       │  Reverse Proxy         │
                       │  Ports: 80, 443        │
                       └───────────────────────┘
```

---

## Performance & Cache

```
Request Flow COM Cache:

Request #1 (sem cache)
├─ Nginx → Backend
├─ Backend → PostgreSQL (query)
├─ Calcula resultado
├─ Armazena em Redis (ex: 15 min TTL)
├─ Retorna ao cliente
└─ Tempo: ~200-500ms

Request #2 (com cache)
├─ Nginx → Backend
├─ Backend → Redis (HIT!)
├─ Retorna do cache
└─ Tempo: ~10-50ms

Cache Strategy:
├─ Rankings: 5 min
├─ Exam stats: 15 min
├─ Results: 30 min
└─ Invalidate: após submit/update
```

---

## Segurança - Camadas

```
┌─────────────────────────────────────────┐
│  Client Validation (Vue.js)             │
│  • Required fields                      │
│  • Email format                         │
│  • Number ranges                        │
└────────────────┬────────────────────────┘
                 │
┌────────────────▼────────────────────────┐
│  HTTP Layer                             │
│  • CORS configurado                     │
│  • Headers customizados                 │
│  • HTTPS ready                          │
└────────────────┬────────────────────────┘
                 │
┌────────────────▼────────────────────────┐
│  API Request Validation                 │
│  • StoreExamRequest                     │
│  • UpdateExamRequest                    │
│  • SubmitExamRequest                    │
│  • Role-based headers                   │
└────────────────┬────────────────────────┘
                 │
┌────────────────▼────────────────────────┐
│  Controller/Service Business Logic      │
│  • Verificações de lógica               │
│  • Queries com model relationships      │
│  • Cálculos de score                    │
└────────────────┬────────────────────────┘
                 │
┌────────────────▼────────────────────────┐
│  Database Level                         │
│  • UNIQUE constraints                   │
│  • Foreign key relationships            │
│  • Check constraints                    │
│  • SQL prepared statements              │
└─────────────────────────────────────────┘
```

---

**Documento criado: 2026-04-29**  
**Status**: ✅ Completo
