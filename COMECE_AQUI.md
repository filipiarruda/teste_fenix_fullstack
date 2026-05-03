# 🎯 COMECE AQUI - GUIA RÁPIDO FENIX EDUCAÇÃO

## 5 Passos para Começar

### 1️⃣ Clonar/Extrair Projeto
```bash
cd fenix-educacao
```

### 2️⃣ Executar Setup Automático (2 min)
```bash
chmod +x init.sh
./init.sh
```

✅ Isso vai fazer:
- Iniciar Docker containers
- Rodar migrations
- Popular dados de teste
- Instalar dependências
- Exibir URLs de acesso

### 3️⃣ Acessar Aplicação
- **Frontend**: http://localhost:5173
- **API**: http://localhost:8000/api/v1

### 4️⃣ Fazer Login
Escolha um dos perfis:

**👨‍🏫 Como Professor:**
- Selecione "Professor"
- Escolha: "Prof. João Silva" ou "Prof. Maria Santos"

**👨‍🎓 Como Aluno:**
- Selecione "Aluno"
- Escolha: "Aluno 1" até "Aluno 10"

### 5️⃣ Explorar
- **Prof**: Criar prova → Ver resultados
- **Aluno**: Responder prova → Ver resultado

---

## 📖 Leitura Rápida

| Documento | Tempo | Conteúdo |
|-----------|-------|----------|
| **README.md** | 5 min | Quick start essencial |
| **SUMARIO_EXECUTIVO.md** | 10 min | Visão geral + métricas |
| **IMPLEMENTACAO_COMPLETA.md** | 10 min | Features implementadas |
| **STATUS_FINAL.txt** | 2 min | Sumário visual |

---

## 🧪 Rodar Testes (5 min)

```bash
# Todos os testes
docker-compose exec backend php artisan test

# Com cobertura (deve ser ≥80%)
docker-compose exec backend php artisan test --coverage
```

---

## 🔨 Problemas Comuns

### Erro: "Port already in use"
```bash
# Mude as portas em docker-compose.yml
# Ou parar containers existentes
docker ps
docker kill <container-id>
```

### Frontend não carrega
```bash
# Verificar logs
docker-compose logs frontend

# Esperar compilar (levou ~30-60 seg)
```

### Banco de dados não conecta
```bash
# Dar mais tempo ao PostgreSQL
sleep 15
docker-compose exec backend php artisan migrate --force
```

---

## 📊 Fluxo Completo para Testar

### Como Professor
1. Login → "Prof. João Silva"
2. Dashboard → "Criar Nova Prova"
3. Preencher:
   - Título: "Minha Primeira Prova"
   - Descrição: "Descrição"
   - Questão 1: "2 + 2 = ?" com alternativas [3, **4**, 5, 6]
4. Salvar
5. Dashboard → "Ver Resultados"
   - Ranking vazio (ninguém respondeu ainda)

### Como Aluno
1. Logout (ou aba incógnita)
2. Login → "Aluno 1"
3. Dashboard → "Responder" na prova do professor
4. Responder cada questão com Previous/Next
5. Clicar "Enviar" na última questão
6. Ver resultado com percentual + gabarito
7. Tentar responder novamente → Mostra resultado anterior (não deixa responder 2x)

### Como Professor (Ver Resultados)
1. Login → "Prof. João Silva"
2. Dashboard → "Ver Resultados"
3. Ver:
   - Média da turma
   - Melhor aluno (Top 1)
   - Ranking com paginação

---

## 🎓 Dados de Teste

**Professores:**
- joao@example.com (senha: qualquer uma)
- maria@example.com

**Alunos:**
- aluno1@example.com até aluno10@example.com

**Provas Pré-carregadas:**
- 6 provas (3 de cada professor)
- 5 questões por prova
- 4 alternativas por questão

---

## 🚀 Comandos Úteis

```bash
# Setup
./init.sh

# Ver status
docker-compose ps

# Ver logs em tempo real
docker-compose logs -f

# Shell no backend
docker-compose exec backend bash

# Resetar banco
docker-compose exec backend php artisan migrate:fresh --seed

# Parar tudo
docker-compose down

# Resetar completamente
docker-compose down -v && docker-compose up -d
```

---

## 📂 Estrutura Básica

```
Projeto/
├── README.md (leia primeiro!)
├── init.sh (execute isso)
├── docker-compose.yml (serviços)
├── backend/ (Laravel)
│   ├── app/Models/ (6 modelos)
│   ├── app/Http/Controllers/ (4 controllers)
│   ├── routes/api.php (15+ endpoints)
│   └── tests/ (6+ testes)
├── frontend/ (Vue.js)
│   ├── src/views/ (7 páginas)
│   ├── src/stores/ (state management)
│   └── src/router/ (roteamento)
└── docker/ (Dockerfiles)
```

---

## 🎯 Destaques Técnicos

✓ **Restrição**: Aluno não pode responder mesma prova 2x
✓ **Automático**: Correção instantânea de score
✓ **Cache**: Redis para rankings
✓ **Seguro**: Validação em múltiplas camadas
✓ **Responsivo**: Mobile-friendly
✓ **Documentado**: 8 arquivos de documentação
✓ **Testado**: ≥80% de cobertura
✓ **Deployável**: Docker Compose pronto

---

## 📞 URLs Importantes

| Recurso | URL |
|---------|-----|
| Frontend | http://localhost:5173 |
| API | http://localhost:8000/api/v1 |
| Health | http://localhost:8000/api/v1/health |
| Database | localhost:5432 (postgres) |
| Cache | localhost:6379 (redis) |

---

## 🎉 Está Tudo Pronto!

```
✅ Backend - Pronto
✅ Frontend - Pronto
✅ Docker - Pronto
✅ Banco - Pronto
✅ Cache - Pronto
✅ Testes - Pronto
✅ Docs - Pronto

👉 Execute: ./init.sh
👉 Acesse: http://localhost:5173
👉 Aproveite!
```

---

## 📚 Leia Também

Para mais detalhes, veja:

1. **README.md** — Instruções completas
2. **SUMARIO_EXECUTIVO.md** — Métricas e status
3. **IMPLEMENTACAO_COMPLETA.md** — Tudo que foi feito
4. **ARQUITETURA_VISUAL.md** — Diagramas e fluxos
5. **VALIDACAO_E_PROXIMOS_PASSOS.md** — Testing e deploy
6. **INDICE_COMPLETO.md** — Índice de arquivos

---

## ⏱️ Tempo Estimado

| Tarefa | Tempo |
|--------|-------|
| Setup | 2 min |
| Leitura README | 5 min |
| Login e explorar | 5 min |
| Criar prova (prof) | 3 min |
| Responder (aluno) | 2 min |
| Rodar testes | 2 min |
| **Total** | **~20 min** |

---

## 🎁 Bônus: Comandos do Database

```bash
# Conectar ao banco (psql)
docker-compose exec postgres psql -U fenix_user -d fenix_db

# Ver tabelas
\dt

# Ver dados de usuários
SELECT * FROM users;

# Ver provas
SELECT * FROM exams;

# Sair
\q
```

---

## ✨ Sucesso!

Você tem uma plataforma **fullstack funcional** pronta para:
- ✅ Testar
- ✅ Aprender
- ✅ Customizar
- ✅ Deployar

**Aproveite!** 🚀

---

**Versão**: 1.0  
**Status**: ✅ Pronto  
**Data**: 2026-04-29
