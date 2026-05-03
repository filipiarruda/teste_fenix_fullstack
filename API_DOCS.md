# Swagger API Documentation

## Endpoints disponíveis

**Acesse a documentação em:**
- http://localhost:8885/api/docs
- http://localhost:8080/api/docs (via nginx proxy)
- http://localhost:8885/api/documentation

## Estrutura da API

### Autenticação
Todas as requisições devem incluir os headers:
- `X-User-Id`: ID do usuário (integer)
- `X-User-Role`: Perfil do usuário (professor | aluno)

### Endpoints Principais

#### Provas (Exams)
- `GET /api/v1/exams` - Listar provas
- `POST /api/v1/exams` - Criar prova
- `GET /api/v1/exams/{id}` - Obter prova
- `PUT /api/v1/exams/{id}` - Atualizar prova
- `DELETE /api/v1/exams/{id}` - Deletar prova

#### Questões (Questions)
- `GET /api/v1/exams/{examId}/questions` - Listar questões
- `POST /api/v1/exams/{examId}/questions` - Adicionar questão

#### Envio de Respostas (Submissions)
- `POST /api/v1/submissions/submit` - Enviar respostas
- `GET /api/v1/submissions/exam/{examId}/result/{userId}` - Obter resultado

#### Análises (Analytics)
- `GET /api/v1/analytics/exam/{examId}/ranking` - Ranking de alunos
- `GET /api/v1/analytics/exam/{examId}/average` - Média da turma

#### Saúde (Health)
- `GET /api/v1/health` - Status da API

## Testar API

```bash
curl -X GET http://localhost:8885/api/v1/health \
  -H "X-User-Id: 1" \
  -H "X-User-Role: professor"
```
