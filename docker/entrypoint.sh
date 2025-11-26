#!/bin/bash
set -e

echo "🚀 Entrando en entrypoint.sh"

# Cloud Run / Docker local
PORT="${PORT:-8080}"
echo "🔧 Puerto detectado: $PORT"

# Ajustar Apache al puerto correcto
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/:80>/:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Diagnóstico mínimo (stdout → capturado por Cloud Run)
echo "🔍 PHP version:"
php -v

echo "🔍 Apache version:"
apache2 -v

echo "🔍 Variables de entorno relevantes:"
env | grep -E "APP_ENV|PROJECT_ROOT|GOOGLE|STRIPE|AUTH0" || true

# Iniciar Apache en foreground (Cloud Run exige 1 único proceso)
echo "🚀 Iniciando Apache (foreground)"
exec apache2-foreground