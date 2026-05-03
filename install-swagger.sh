#!/bin/bash

echo "========================================"
echo "Instalando L5-Swagger para Laravel"
echo "========================================"

cd backend

echo "1. Instalando dependência..."
composer require darkaonline/l5-swagger

echo "2. Publicando configuração..."
php artisan vendor:publish --provider "L5\\Swagger\\SwaggerServiceProvider"

echo "3. Gerando documentação..."
php artisan l5-swagger:generate

echo ""
echo "✓ L5-Swagger instalado com sucesso!"
echo ""
echo "Acesse em:"
echo "  http://localhost:8885/api/documentation"
echo "  http://localhost:8080/api/documentation"
echo ""
