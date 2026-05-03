# Instalação de Swagger com L5-Swagger (Padrão Laravel)

## ✅ Estrutura Implementada

### Controllers com Anotações OpenAPI
- ✅ `app/Http/Controllers/Controller.php` - Definição global de OpenAPI
- ✅ `app/Http/Controllers/ExamController.php` - 5 endpoints CRUD
- ✅ `app/Http/Controllers/QuestionController.php` - 2 endpoints
- ✅ `app/Http/Controllers/SubmissionController.php` - 2 endpoints
- ✅ `app/Http/Controllers/AnalyticsController.php` - 2 endpoints
- ✅ `app/Http/Controllers/HealthController.php` - 1 endpoint
- ✅ `app/Http/Controllers/Schemas.php` - Definições de schema

### Rotas
- ✅ `routes/api.php` - Todas as rotas documentadas
- ✅ `routes/web.php` - Sem Swagger hardcoded (padrão Laravel)

## 🚀 Instalação (Execute no Container)

```bash
chmod +x install-swagger.sh
docker-compose exec backend bash /app/install-swagger.sh
```

## 📖 Ou Manualmente

```bash
cd backend

# 1. Instalar L5-Swagger
composer require darkaonline/l5-swagger

# 2. Publicar configuração
php artisan vendor:publish --provider "L5\\Swagger\\SwaggerServiceProvider"

# 3. Gerar documentação (toda vez que modificar anotações)
php artisan l5-swagger:generate
```

## 🔗 Acessar Documentação

```
http://localhost:8885/api/documentation
http://localhost:8080/api/documentation (via nginx)
```

## 📝 Adicionar Novos Endpoints

1. Criar método no controller com anotação `@OA\...`
2. Adicionar rota em `routes/api.php`
3. Gerar documentação: `php artisan l5-swagger:generate`

### Exemplo

```php
/**
 * @OA\Get(
 *      path="/api/v1/exemplo",
 *      tags={"Exemplo"},
 *      summary="Descrição do endpoint",
 *      @OA\Response(response=200, description="Sucesso")
 * )
 */
public function exemplo()
{
    return response()->json(['message' => 'Sucesso']);
}
```

## ✨ Vantagens do L5-Swagger

- ✅ Padrão do Laravel
- ✅ Documentação junto com código
- ✅ Gerar automaticamente JSON OpenAPI
- ✅ Interface Swagger UI integrada
- ✅ Cache de documentação
- ✅ Validação de anotações

## 📦 Configuração (config/l5-swagger.php)

Será criado automaticamente com valores padrão:
- URL: `/api/documentation`
- Formato: JSON
- Host: `L5_SWAGGER_CONST_HOST`

## 🧪 Testar API

```bash
curl http://localhost:8885/api/v1/health \
  -H "X-User-Id: 1" \
  -H "X-User-Role: professor"
```

## 📊 Total de Endpoints Documentados

- Exams: 5 (GET, POST, GET{id}, PUT{id}, DELETE{id})
- Questions: 2 (GET, POST)
- Submissions: 2 (POST, GET)
- Analytics: 2 (GET, GET)
- Health: 1 (GET)

**Total: 14 endpoints com anotações OpenAPI** ✅
