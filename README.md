# Fenix Educação - Plataforma de Provas Online

## Sobre

Fenix Educação é uma plataforma fullstack para criação, execução e análise de provas online com correção automática. O sistema permite que professores criem provas com múltiplas questões e analisa o desempenho dos alunos em tempo real.

## Stack Tecnológica

- **Backend**: PHP 8.4 com Laravel 11
- **Frontend**: Vue.js 3 com Vite
- **Banco de Dados**: PostgreSQL 15
- **Cache**: Redis 7
- **Infraestrutura**: Docker & Docker Compose
- **Proxy Reverso**: Nginx

## Requisitos

- Docker & Docker Compose
- Git

## Instalação e Execução

### 1. Clone ou extraia o projeto

```bash
cd fenix-educacao
```

### 2. Configure as variáveis de ambiente

As variáveis já estão pré-configuradas em `.env`. Você pode ajustá-las conforme necessário.

### 3. Inicie os contêineres

```bash
docker-compose up -d
```

Isso iniciará:
- PostgreSQL (porta 5432)
- Redis (porta 6379)
- Backend Laravel (porta 8000)
- Frontend Vue.js (porta 5173)
- Nginx (porta 80)

### 4. Aguarde a inicialização

O Backend executará automaticamente as migrations e seeders. Isso pode levar alguns minutos na primeira execução.

```bash
docker-compose logs -f backend
```

### 5. Acesse a aplicação

- **Frontend**: http://localhost:5173
- **API**: http://localhost:8000/api/v1
- **Health Check**: http://localhost:8000/api/v1/health

## Dados de Teste

A aplicação já vem com dados de teste pré-carregados:

### Professores
- Prof. João Silva (joao@example.com)
- Prof. Maria Santos (maria@example.com)

### Alunos
- Aluno 1-10 (aluno1@example.com até aluno10@example.com)

### Provas
Cada professor tem 3 provas com 5 questões cada. Alguns alunos já responderam essas provas para teste.

## Funcionalidades

### Para Professores
✅ Criar, editar e deletar provas
✅ Adicionar questões com múltiplas alternativas
✅ Marcar a resposta correta por questão
✅ Visualizar dashboard com resultados
✅ Ver média da turma
✅ Ver melhor pontuação (Top 1)
✅ Ver ranking de alunos com paginação

### Para Alunos
✅ Visualizar provas disponíveis
✅ Realizar prova (apenas uma vez)
✅ Submeter respostas
✅ Visualizar resultado imediato
✅ Ver pontuação e percentual de acertos
✅ Visualizar gabarito após submissão

## Arquitetura

### Backend (Laravel)

```
backend/
├── app/
│   ├── Models/          # Eloquent models (User, Exam, Question, etc)
│   ├── Http/
│   │   ├── Controllers/ # API controllers
│   │   └── Requests/    # Form requests (validação)
│   ├── Services/        # Business logic
│   └── Repositories/    # Data access
├── database/
│   ├── migrations/      # Schema migrations
│   ├── factories/       # Model factories
│   └── seeders/         # Database seeders
├── routes/
│   └── api.php          # API routes
└── tests/
    ├── Unit/            # Unit tests
    └── Feature/         # Feature tests
```

### Frontend (Vue.js)

```
frontend/
├── src/
│   ├── components/      # Vue components
│   ├── views/           # Page components
│   ├── stores/          # Pinia state management
│   ├── services/        # API service
│   ├── router/          # Vue Router
│   ├── App.vue          # Root component
│   └── main.js          # Entry point
├── index.html           # HTML template
└── vite.config.js       # Vite configuration
```

## Fluxo de Dados

### Submissão de Prova

1. Aluno seleciona alternativas
2. Frontend envia POST `/api/v1/submissions/submit` com respostas
3. Backend valida que aluno não respondeu antes
4. Calcula acertos comparando com gabarito
5. Cria `ExamResult` com pontuação
6. Retorna resultado com percentual
7. Frontend exibe resultado na tela

### Cache com Redis

- Listagem de provas (invalidada ao criar/atualizar)
- Rankings e métricas (invalidada ao submeter prova)
- Resultados individuais (invalidada ao submeter)

## Documentação da API

### Exames

```
GET    /api/v1/exams              # Listar provas
POST   /api/v1/exams              # Criar prova (professor)
GET    /api/v1/exams/:id          # Obter prova com questões
PUT    /api/v1/exams/:id          # Atualizar prova
DELETE /api/v1/exams/:id          # Deletar prova
```

### Questões

```
GET    /api/v1/exams/:examId/questions      # Listar questões
POST   /api/v1/exams/:examId/questions      # Criar questão
GET    /api/v1/questions/:id                # Obter questão
PUT    /api/v1/questions/:id                # Atualizar questão
DELETE /api/v1/questions/:id                # Deletar questão
```

### Submissões

```
POST   /api/v1/submissions/submit                        # Submeter prova
GET    /api/v1/submissions/exam/:examId/user/:userId     # Obter respostas
GET    /api/v1/submissions/exam/:examId/result/:userId   # Obter resultado
```

### Analytics

```
GET /api/v1/analytics/exam/:examId                # Estatísticas gerais
GET /api/v1/analytics/exam/:examId/ranking        # Ranking com paginação
GET /api/v1/analytics/exam/:examId/average        # Média da turma
GET /api/v1/analytics/exam/:examId/top            # Melhor pontuação
```

## Testes

### Backend

```bash
# Rodar todos os testes
docker-compose exec backend php artisan test

# Com coverage
docker-compose exec backend php artisan test --coverage

# Um arquivo específico
docker-compose exec backend php artisan test tests/Unit/ExamServiceTest.php
```

Alvo: Mínimo 80% de cobertura de código.

## Performance

- **Cache**: Redis cacheia listagem de provas, rankings e resultados
- **Índices**: Banco de dados otimizado com índices nas chaves estrangeiras
- **Paginação**: Todos os endpoints retornam resultados paginados (15 itens por página)
- **Lazy Loading**: Relacionamentos carregados sob demanda

## Restrições Implementadas

### Aluno não pode repetir prova
- Constraint `unique(exam_id, user_id)` na tabela `exam_results`
- Validação no serviço antes de submeter

## Parar os Contêineres

```bash
docker-compose down
```

Para remover volumes (deletar dados):

```bash
docker-compose down -v
```

## Troubleshooting

### Backend não consegue conectar ao PostgreSQL

```bash
docker-compose logs postgres
docker-compose exec postgres pg_isready
```

### Frontend não carrega

```bash
docker-compose logs frontend
# Verifique se porta 5173 está disponível
```

### Limpar e recomeçar

```bash
docker-compose down -v
docker-compose up -d
```

## Autor

Desenvolvido como desafio Fenix - Desenvolvedor Fullstack

## Licença

MIT
