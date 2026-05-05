# frontend/docker-entrypoint.sh
#!/bin/sh
set -e

cd /app

# Instalar dependências se não estiverem
if [ ! -d "node_modules" ]; then
  npm install
fi

# Rodar dev server
npm run dev -- --host