# Despliegue de STREAM+

Este documento explica cómo desplegar STREAM+ tanto en entornos locales como en producción, incluyendo opciones para facilitar el despliegue en otros dispositivos.

## Opción 1: Despliegue con Docker (Recomendado - Más Fácil)

Docker permite empaquetar la aplicación y la base de datos en contenedores, facilitando el despliegue en cualquier dispositivo con Docker instalado.

### Requisitos Previos
- [Docker](https://www.docker.com/get-started) instalado
- [Docker Compose](https://docs.docker.com/compose/install/) instalado

### Archivos Necesarios
- `Dockerfile` (para la aplicación PHP)
- `docker-compose.yml` (para orquestar servicios)
- `.dockerignore` (opcional, para excluir archivos)

### Pasos para Desplegar con Docker

1. **Instalar Docker y Docker Compose**
   - Descarga e instala Docker Desktop desde el sitio oficial
   - Docker Compose viene incluido en Docker Desktop

2. **Preparar los archivos**
   - Asegúrate de tener los archivos `Dockerfile` y `docker-compose.yml` en la raíz del proyecto

3. **Construir y ejecutar**
   ```bash
   # Desde la raíz del proyecto
   docker-compose up --build
   ```

4. **Acceder a la aplicación**
   - Aplicación: `http://localhost:8080`
   - phpMyAdmin: `http://localhost:8081` (usuario: root, contraseña: rootpassword)

### Archivos Docker incluidos:
- **Dockerfile**: Configura el contenedor Apache+PHP
- **docker-compose.yml**: Define servicios (web, db, phpmyadmin)

## Opción 2: Despliegue Manual en Producción

### Hosting con PHP y MySQL

#### Opción A: Hosting Compartido (Fácil)
1. **Elegir proveedor**: SiteGround, Hostinger, o similar con PHP 7.4+ y MySQL
2. **Subir archivos**: Usa FTP para subir la carpeta del proyecto a `public_html`
3. **Crear base de datos**: Desde el panel de control del hosting
4. **Importar datos**: Usa phpMyAdmin del hosting para importar los SQL
5. **Configurar database.php**: Actualizar credenciales de producción

#### Opción B: VPS o Servidor Dedicado
1. **Instalar servidor web**: Apache/Nginx + PHP 7.4+ + MySQL
2. **Configurar dominio**: Apuntar DNS al servidor
3. **SSL**: Instalar certificado Let's Encrypt
4. **Subir proyecto**: Git clone o FTP
5. **Configurar BD**: Crear BD e importar SQL

### Variables de Entorno
Para producción, considera usar variables de entorno en lugar de credenciales hardcodeadas:

```php
// config/database.php
$host = getenv('DB_HOST') ?: 'localhost';
$db = getenv('DB_NAME') ?: 'daw_streaming';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
```

## Opción 3: Despliegue Automatizado

### Script de Despliegue (deploy.sh)
```bash
#!/bin/bash
# Script de despliegue automático

echo "🚀 Iniciando despliegue de STREAM+..."

# Instalar dependencias
composer install  # Si usas Composer

# Configurar base de datos
mysql -u root -p < database_seed_relacionado.sql
mysql -u root -p < database_seed_stream_plus.sql

# Configurar permisos
chmod 755 -R .
chown www-data:www-data -R .

echo "✅ Despliegue completado!"
```

### GitHub Actions (CI/CD)
Para despliegue automático en cada push:

```yaml
# .github/workflows/deploy.yml
name: Deploy to Production
on:
  push:
    branches: [ main ]
jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
    - uses: actions/checkout@v2
    - name: Deploy to server
      uses: appleboy/ssh-action@master
      with:
        host: ${{ secrets.HOST }}
        username: ${{ secrets.USERNAME }}
        key: ${{ secrets.KEY }}
        script: |
          cd /var/www/stream-plus
          git pull origin main
          # Ejecutar script de despliegue
```

## Configuración por Entorno

### Desarrollo (Local)
- XAMPP o Docker
- Base de datos local
- Errores visibles

### Producción
- Servidor web optimizado
- Base de datos dedicada
- Errores ocultos
- Cache activado
- HTTPS obligatorio

## Solución de Problemas Comunes

### Error de Conexión BD
- Verificar credenciales en `database.php`
- Asegurar que MySQL esté ejecutándose
- Comprobar permisos de usuario BD

### Permisos de Archivos
```bash
# En Linux/Mac
sudo chown -R www-data:www-data /ruta/al/proyecto
sudo chmod -R 755 /ruta/al/proyecto
```

### Rendimiento
- Activar OPcache en PHP
- Usar CDN para imágenes
- Optimizar consultas SQL
- Implementar cache (Redis/Memcached)

## Recomendaciones de Seguridad

### Antes de Desplegar
1. **Cambiar credenciales por defecto**
2. **Eliminar archivos de desarrollo** (como `database_seed_*.sql`)
3. **Configurar HTTPS**
4. **Actualizar PHP y dependencias**
5. **Configurar firewall**
6. **Hacer backup regular**

### Monitoreo
- Logs de errores: `tail -f /var/log/apache2/error.log`
- Monitoreo de BD: `SHOW PROCESSLIST;`
- Uso de recursos: `htop` o `top`

## Costos Aproximados

### Opción Gratuita
- **000webhost** o **InfinityFree**: Hosting gratuito limitado
- **Docker**: Gratuito para desarrollo

### Opción de Pago (~10-50€/mes)
- **SiteGround**: Compartido desde 5€/mes
- **DigitalOcean**: VPS desde 12€/mes
- **Heroku**: PaaS desde 7€/mes (con add-ons)

---

**STREAM+ Deployment Guide**  
*Facilitando el despliegue de tu plataforma de streaming*