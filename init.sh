#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}===================================================${NC}"
echo -e "${BLUE}   Fenix Educação - Setup Inicial${NC}"
echo -e "${BLUE}===================================================${NC}\n"

# Check if docker is installed
if ! command -v docker &> /dev/null; then
    echo -e "${RED}Docker não está instalado!${NC}"
    exit 1
fi

echo -e "${BLUE}1. Iniciando os contêineres...${NC}"
docker-compose up -d

echo -e "\n${BLUE}2. Aguardando inicialização do banco de dados...${NC}"
sleep 10

echo -e "\n${BLUE}3. Executando migrations e seeders...${NC}"
docker compose exec -T backend php artisan migrate --force
docker compose exec -T backend php artisan db:seed --force

echo -e "\n${BLUE}4. Gerando chave da aplicação...${NC}"
docker compose exec -T backend php artisan key:generate --force

echo -e "\n${BLUE}5. Instalando dependências do frontend...${NC}"
docker compose exec -T frontend npm install

echo -e "\n${GREEN}===================================================${NC}"
echo -e "${GREEN}   Setup concluído com sucesso!${NC}"
echo -e "${GREEN}===================================================${NC}\n"

echo -e "Endpoints disponíveis:"
echo -e "  Frontend: ${BLUE}http://localhost:8004${NC}"
echo -e "  API:      ${BLUE}http://localhost:8885/api/v1${NC}"
echo -e "  Health:   ${BLUE}http://localhost:8885/api/v1/health${NC}\n"

echo -e "Comandos úteis:"
echo -e "  Ver logs:        ${BLUE}docker compose logs -f${NC}"
echo -e "  Rodar testes:    ${BLUE}docker compose exec backend php artisan test${NC}"
echo -e "  Parar tudo:      ${BLUE}docker compose down${NC}"
echo -e "  Limpar e resetar:${BLUE}docker compose down -v && docker compose up -d${NC}\n"
