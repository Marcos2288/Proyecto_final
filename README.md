# STREAM+

## Descripción

STREAM+ es una plataforma de streaming integral desarrollada como proyecto final del ciclo DAW (Desarrollo de Aplicaciones Web). Combina un catálogo multimedia de películas y series con una tienda de merchandising y un foro comunitario. Incluye funcionalidades avanzadas como modo anti-spoiler, carrito de compras persistente y panel de administrador.

### Características Principales
- **Catálogo Multimedia**: Películas y series con episodios organizados
- **Tienda de Productos**: Merchandising relacionado con el contenido
- **Foro Comunitario**: Espacio para discusiones entre usuarios
- **Modo Anti-Spoiler**: Oculta productos de contenido no visto
- **Autenticación Segura**: Registro y login con sesiones
- **Panel de Administrador**: Para gestionar contenido y productos

## Tecnologías Utilizadas
- **Backend**: PHP 7.4+ con PDO
- **Base de Datos**: MySQL 8.0
- **Frontend**: HTML5, CSS3, JavaScript vanilla
- **Servidor**: XAMPP (Apache + MySQL)

## Requisitos Previos

Antes de ejecutar el proyecto, asegúrate de tener instalado:

- [XAMPP](https://www.apachefriends.org/download.html) (versión 8.0 o superior)
- Navegador web moderno (Chrome, Firefox, Edge)
- Editor de código (opcional, recomendado VS Code)

## Instalación y Configuración

### Paso 1: Instalar XAMPP
1. Descarga e instala XAMPP desde el sitio oficial.
2. Inicia los módulos Apache y MySQL desde el panel de control de XAMPP.

### Paso 2: Ubicar el Proyecto
1. Copia la carpeta `proyecto_final_mejorado` dentro de `C:\xampp\htdocs\`.
   - La ruta completa debería ser: `C:\xampp\htdocs\proyecto_final_mejorado`

### Paso 3: Configurar la Base de Datos

**Importante**: La base de datos NO se configura automáticamente. Debes seguir estos pasos manualmente para que el proyecto funcione.

1. Abre tu navegador y ve a `http://localhost/phpmyadmin`
2. Crea una nueva base de datos llamada `daw_streaming` (collation: utf8_general_ci)
3. Selecciona la base de datos `daw_streaming`
4. Ve a la pestaña "Importar"
5. **Importa primero `database_seed_relacionado.sql`** (este archivo crea todas las tablas necesarias)
6. **Luego importa `database_seed_stream_plus.sql`** (este archivo inserta los datos de ejemplo: usuarios, contenidos, productos, etc.)

**¿Por qué manual?** En proyectos educativos como este, la configuración manual permite aprender sobre bases de datos y entender el proceso de despliegue en diferentes entornos.

### Paso 4: Verificar Configuración
1. Abre el archivo `config/database.php`
2. Verifica que las credenciales de conexión sean correctas:
   ```php
   $host = 'localhost';
   $db = 'daw_streaming';
   $user = 'root';
   $pass = '';
   ```
   (Por defecto en XAMPP, la contraseña del usuario root está vacía)

## Cómo Ejecutar el Proyecto

1. Asegúrate de que Apache y MySQL estén ejecutándose en XAMPP.
2. Abre tu navegador web.
3. Ve a la URL: `http://localhost/proyecto_final_mejorado`
4. Deberías ver la página de inicio de STREAM+.

### Acceso de Prueba
- **Usuario de prueba**: Regístrate o usa las credenciales si hay usuarios en el seed.
- **Panel de Administrador**: Accede a `/developer` para crear contenido (requiere estar logueado).

## Estructura del Proyecto

```
proyecto_final_mejorado/
├── index.php                    # Página de inicio
├── logout.php                   # Cierre de sesión
├── config/
│   ├── database.php            # Configuración de BD
│   └── helpers.php             # Funciones auxiliares
├── controllers/                # Lógica de negocio
├── models/                     # Modelos de datos
├── views/                      # Plantillas HTML
├── public/                     # CSS, JS, imágenes
├── database_seed_relacionado.sql  # Estructura BD
├── database_seed_stream_plus.sql  # Datos de ejemplo
└── documentacion_proyecto.md   # Documentación completa
```

## Uso Básico

### Navegación
- **Inicio**: Página principal con novedades y productos destacados
- **Cine**: Catálogo completo de películas y series
- **Tienda**: Productos de merchandising
- **Foro**: Comunidad de usuarios
- **Perfil**: Gestión de cuenta y preferencias

### Funcionalidades Clave
1. **Registro/Login**: Crea una cuenta para acceder a todas las funciones
2. **Explorar Contenido**: Navega por películas y series, marca episodios como vistos
3. **Comprar Productos**: Agrega items al carrito (persiste en sesión)
4. **Participar en Foro**: Crea temas y responde a otros usuarios
5. **Modo Anti-Spoiler**: Actívalo en tu perfil para evitar spoilers

## Solución de Problemas

### Error de Conexión a BD
- Verifica que MySQL esté ejecutándose en XAMPP
- Confirma que la base de datos `daw_streaming` existe
- Revisa las credenciales en `config/database.php`

### Página en Blanco
- Asegúrate de que PHP esté habilitado en XAMPP
- Verifica la ruta del proyecto en `htdocs`
- Revisa los logs de Apache en XAMPP

### Problemas de Permisos
- Ejecuta XAMPP como administrador
- Verifica que los archivos no estén bloqueados por antivirus

## Desarrollo

Para contribuir o modificar el proyecto:
1. Clona el repositorio (si está en Git)
2. Instala las dependencias (XAMPP)
3. Sigue los pasos de instalación
4. Realiza cambios en el código
5. Prueba en localhost

## Licencia

Este proyecto es educativo y no tiene licencia específica. Úsalo para aprendizaje y referencia.

## Despliegue

Para opciones de despliegue más avanzadas o en otros dispositivos, consulta el archivo `DEPLOYMENT.md` que incluye:

- **Despliegue con Docker** (recomendado para facilidad)
- **Hosting en producción**
- **Scripts de automatización**
- **Configuración por entorno**

### Despliegue Rápido con Docker

Si tienes Docker instalado, puedes desplegar todo automáticamente:

```bash
# Desde la raíz del proyecto
docker-compose up --build
```

Luego accede a `http://localhost:8080`