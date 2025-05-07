#!/bin/bash
set -e

LOG_FILE="/var/www/html/logs/entrypoint.log"
mkdir -p "$(dirname "$LOG_FILE")"
touch "$LOG_FILE"

log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

log "🚀 Entrando en entrypoint.sh"

# Default port fallback
PORT="${PORT:-8080}"
log "🔧 Usando puerto: $PORT"

# Configurar Apache dinámicamente para el puerto correcto
log "🛠️  Ajustando Apache para usar el puerto $PORT"
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf || log "❌ Error modificando ports.conf"
sed -i "s/:80>/:${PORT}>/" /etc/apache2/sites-available/000-default.conf || log "❌ Error modificando 000-default.conf"

# Verificar DocumentRoot
if [ ! -f "/var/www/html/public/index.php" ]; then
    log "❌ FALTA: /public/index.php no encontrado. Revisa COPY en Dockerfile"
    ls -la /var/www/html/public | tee -a "$LOG_FILE"
else
    log "✅ /public/index.php detectado correctamente"
fi

# Crear carpeta de logs si no existe
mkdir -p /var/www/html/logs/apache2 /var/log/supervisord
chmod -R 777 /var/www/html/logs /var/log/supervisord

log "📂 Logs Apache → /var/www/html/logs/apache2"
log "📂 Logs Supervisor → /var/log/supervisord"

# Diagnóstico de entorno
log "🔍 PHP:"
php -v | tee -a "$LOG_FILE"

log "🔍 Apache:"
apache2 -v | tee -a "$LOG_FILE"

log "🔍 Variables de entorno:"
env | tee -a "$LOG_FILE"

log "✅ Ejecutando supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf