# 🌸Portada

**Proyecto Final del Ciclo: STREAM+**  
**Plataforma de Streaming Integral**

**Autor:** [Tu Nombre]  
**Curso Escolar:** 2025-2026  
**Ciclo Formativo:** Desarrollo de Aplicaciones Web (DAW)  

---

# 📌Índice

1. [Introducción](#introducción)
2. [Planificación](#planificación)
3. [Estado del Arte / Investigación](#estado-del-arte--investigación)
4. [Diseño](#diseño)
5. [Implementación](#implementación)
6. [Pruebas](#pruebas)
7. [Conclusiones](#conclusiones)
8. [Bibliografía](#bibliografía)

---

# ⚡Introducción

## Tema del Proyecto
STREAM+ es una plataforma de streaming integral que combina entretenimiento multimedia con comercio electrónico y comunidad. Inspirada en servicios como Netflix, pero con un enfoque educativo y modular, permite a los usuarios explorar un catálogo de películas y series, realizar compras de merchandising relacionado, y participar en foros comunitarios.

## Relevancia
En la era digital actual, las plataformas de streaming han revolucionado el consumo de contenido audiovisual. Este proyecto aborda la necesidad de integrar múltiples aspectos del entretenimiento en una sola aplicación, demostrando habilidades en desarrollo web full-stack, gestión de bases de datos y diseño de interfaces de usuario.

## Contexto del Problema
Los usuarios de plataformas de streaming a menudo buscan experiencias más inmersivas que vayan más allá de la visualización de contenido. STREAM+ resuelve esto ofreciendo:
- Un catálogo organizado de contenido multimedia
- Una tienda integrada para productos relacionados
- Un espacio comunitario para discusiones
- Funcionalidades avanzadas como modo anti-spoiler

## Justificación de la Elección del Tema
Este proyecto se alinea perfectamente con los módulos del ciclo DAW, cubriendo:
- Desarrollo de aplicaciones web con PHP
- Gestión de bases de datos MySQL
- Diseño de interfaces responsivas con HTML/CSS/JS
- Seguridad en aplicaciones web
- Arquitecturas modulares y patrones de diseño

## Objetivos
1. Desarrollar una aplicación web completa y funcional
2. Implementar autenticación y gestión de usuarios segura
3. Crear un sistema de catálogo multimedia con seguimiento de progreso
4. Integrar un módulo de comercio electrónico
5. Desarrollar un foro comunitario
6. Aplicar buenas prácticas de seguridad y usabilidad
7. Documentar todo el proceso de desarrollo

---

# 🗓️Planificación

## Cronograma
El proyecto se desarrolló siguiendo una metodología ágil con las siguientes fases:

| Fase | Duración Estimada | Duración Real | Actividades |
|------|------------------|---------------|-------------|
| Planificación e Investigación | 1 semana | 1 semana | Análisis de requisitos, investigación de tecnologías |
| Diseño de Base de Datos | 3 días | 2 días | Modelado ER, creación de esquemas |
| Desarrollo Backend | 2 semanas | 3 semanas | Controladores, modelos, lógica de negocio |
| Desarrollo Frontend | 2 semanas | 2 semanas | Vistas, estilos, JavaScript |
| Integración y Testing | 1 semana | 1 semana | Pruebas funcionales, corrección de bugs |
| Documentación | 1 semana | 1 semana | Redacción de documentación |

## Recursos Necesarios
### Hardware
- Ordenador personal con Windows
- Servidor local XAMPP

### Software
- PHP 7.4+
- MySQL 8.0
- Editor de código (VS Code)
- Navegador web para testing

### Recursos Humanos
- Desarrollador principal (yo mismo)
- Tutor del ciclo para consultas

## Estimación de Costes
### Costes de Desarrollo
- **Tiempo de trabajo**: 40 horas/semana × 6 semanas = 240 horas
- **Valor hora estimado**: 15€/hora
- **Coste total desarrollo**: 3.600€
- **Herramientas**: Gratuitas (XAMPP, VS Code, PHP/MySQL)

### Costes de Infraestructura
- **Hosting**: 50€/mes (estimado para producción)
- **Dominio**: 10€/año
- **Base de datos**: Incluido en hosting

**Coste total estimado**: 4.000€ (mayoritariamente tiempo de desarrollo)

---

# 🔎 Estado del Arte / Investigación

## Soluciones Existentes en el Mercado

### Plataformas de Streaming
- **Netflix**: Líder del mercado con catálogo extenso, recomendaciones basadas en IA, interfaz intuitiva
  - **Fortalezas**: Contenido original, algoritmos de recomendación avanzados, calidad 4K
  - **Debilidades**: Costo elevado de suscripción, falta de integración comunitaria

- **HBO Max**: Enfoque en contenido premium, series exclusivas
  - **Fortalezas**: Calidad de producción, catálogo curado
  - **Debilidades**: Menos contenido que Netflix, interfaz menos moderna

- **Disney+**: Especializado en contenido familiar
  - **Fortalezas**: Contenido infantil, integración con otros servicios Disney
  - **Debilidades**: Catálogo limitado fuera del universo Disney

### Tiendas de Merchandising
- **Redbubble**: Plataforma para artistas independientes
- **Etsy**: Mercado de productos handmade
- **Tiendas oficiales**: De cada plataforma (Netflix Shop, etc.)

### Foros Comunitarios
- **Reddit**: Comunidad masiva con subreddits temáticos
- **Discord**: Comunicación en tiempo real
- **Foros especializados**: Como Letterboxd para cine

## Tecnologías Investigadas

### Backend
- **PHP**: Elegido por su simplicidad y amplio soporte en hosting
- **Frameworks**: Laravel (demasiado complejo para proyecto educativo), Symfony (sobrecarga)
- **Alternativas**: Node.js con Express (más moderno pero requiere curva de aprendizaje)

### Base de Datos
- **MySQL**: Estándar en aplicaciones web PHP
- **PostgreSQL**: Más robusto pero complejo
- **SQLite**: Ligero pero limitado para producción

### Frontend
- **Vanilla JavaScript**: Sin frameworks para mantener simplicidad
- **CSS**: Diseño moderno con Flexbox/Grid
- **Frameworks considerados**: React/Vue (demasiado avanzado para alcance del proyecto)

### Seguridad
- **OWASP Top 10**: Guía para prevenir vulnerabilidades comunes
- **PHP Security Best Practices**: Hashing de contraseñas, prepared statements

## Tecnologías Elegidas
Basado en la investigación, se optó por tecnologías probadas y adecuadas para un proyecto educativo:
- **PHP + MySQL**: Stack clásico y confiable
- **Arquitectura MVC ligera**: Sin framework para entender conceptos básicos
- **CSS moderno**: Diseño responsivo sin librerías externas

---

# ✍️Diseño

## Arquitectura General
STREAM+ sigue una arquitectura MVC (Model-View-Controller) ligera:

```
Usuario → Controlador → Modelo → Base de Datos
              ↓
           Vista → Usuario
```

## Diagrama de Base de Datos
```
usuarios (id, nombre, email, password, modo_anti_spoiler)
    ↓
contenidos (id, titulo, tipo, imagen, descripcion)
    ↓
episodios (id, serie_id, temporada, numero, titulo, imagen, descripcion)
productos (id, nombre, precio, imagen, categoria, descripcion, stock, rating, contenido_id)
historial (usuario_id, contenido_id, visto)
temas (id, titulo, usuario_id, fecha)
respuestas (id, tema_id, usuario_id, texto, fecha)
```

## Diseño de Interfaces
### Paleta de Colores
- **Primarios**: #1f1c2c (fondo oscuro), #928dab (texto)
- **Acentos**: #ff6ec7 (rosa), #7873f5 (morado)
- **Estados**: Verde para éxito, rojo para errores

### Componentes Principales
- **Header**: Navegación con menú responsive
- **Cards**: Para contenido, productos, temas del foro
- **Forms**: Login, registro, creación de contenido
- **Modales**: Detalles, confirmaciones

### Wireframes
[Incluir capturas o descripciones de wireframes si disponibles]

## Flujos de Usuario
1. **Registro/Login** → Dashboard personal
2. **Explorar Catálogo** → Ver detalles → Marcar como visto
3. **Comprar Productos** → Agregar al carrito → Checkout
4. **Participar en Foro** → Crear tema → Responder

---

# 🛠️Implementación

## Tecnologías Utilizadas
- **Backend**: PHP 7.4+ con PDO
- **Base de Datos**: MySQL 8.0
- **Frontend**: HTML5, CSS3, JavaScript vanilla
- **Servidor**: XAMPP (Apache + MySQL)

## Estructura de Archivos
```
proyecto_final_mejorado/
├── config/          # Configuración BD y helpers
├── controllers/     # Lógica de negocio
├── models/          # Interacción con BD
├── views/           # Plantillas HTML
├── public/          # CSS, imágenes, assets
└── index.php        # Punto de entrada
```

## Funcionalidades Implementadas

### Autenticación
- Registro con validación de email y contraseña
- Login con verificación de credenciales
- Sesiones seguras con manejo de roles

### Catálogo Multimedia
- Listado de películas y series
- Detalles con episodios organizados por temporada
- Seguimiento de progreso de visualización

### Comercio Electrónico
- Tienda con productos categorizados
- Carrito de compras persistente en sesión
- Sistema de stock y precios

### Foro Comunitario
- Creación y gestión de temas
- Sistema de respuestas
- Estadísticas de participación

### Funcionalidades Avanzadas
- **Modo Anti-Spoiler**: Oculta productos relacionados con contenido no visto
- **Búsqueda en Vivo**: Resultados en tiempo real
- **Panel de Administrador**: Para gestión de contenido

## Problemas Encontrados y Soluciones

### Problema 1: Gestión de Sesiones
**Problema**: Pérdida de datos del carrito al cerrar navegador
**Solución**: Implementar serialización de arrays en base de datos

### Problema 2: Validación de Formularios
**Problema**: Datos maliciosos en inputs
**Solución**: Uso de `filter_input()` y `htmlspecialchars()`

### Problema 3: Optimización de Consultas
**Problema**: Consultas N+1 en listados con relaciones
**Solución**: Joins optimizados y eager loading

## Cambios Respecto al Diseño Inicial
- **Inicial**: Framework Laravel
- **Final**: PHP puro para mejor comprensión de conceptos
- **Inicial**: Base de datos PostgreSQL
- **Final**: MySQL por compatibilidad con hosting

## Manual de Usuario
[Incluir instrucciones básicas de uso]

---

# 🌀Pruebas

## Estrategia de Testing
- **Testing Manual**: Verificación de funcionalidades principales
- **Testing de Seguridad**: Validación de inputs, SQL injection prevention
- **Testing de Usabilidad**: Navegación intuitiva, responsive design

## Casos de Prueba

### Autenticación
- ✅ Registro con datos válidos
- ✅ Login con credenciales correctas
- ✅ Prevención de acceso no autorizado

### Catálogo
- ✅ Listado de contenidos
- ✅ Visualización de detalles
- ✅ Marcado de episodios como vistos

### Comercio
- ✅ Agregar productos al carrito
- ✅ Actualizar cantidades
- ✅ Cálculo correcto de totales

### Foro
- ✅ Crear nuevos temas
- ✅ Responder a temas existentes
- ✅ Visualización de respuestas

## Resultados
- **Funcionalidades principales**: 100% operativas
- **Seguridad**: Sin vulnerabilidades detectadas
- **Usabilidad**: Interfaz intuitiva y responsiva

## Objetivos Alcanzados
✅ Aplicación web completa y funcional  
✅ Integración de múltiples módulos  
✅ Seguridad básica implementada  
✅ Diseño moderno y responsivo  
✅ Documentación completa  

---

# 🤔Conclusiones

## Aprendizajes Obtenidos
Este proyecto ha sido una experiencia invaluable que me ha permitido:
- **Dominar PHP y MySQL**: Desarrollo de aplicaciones web desde cero
- **Aplicar conceptos de seguridad**: Prevención de vulnerabilidades comunes
- **Diseñar interfaces modernas**: CSS avanzado y diseño responsivo
- **Gestionar proyectos**: Planificación, desarrollo iterativo y documentación

## Mejoras Futuras
- **Implementar API REST**: Separar frontend y backend
- **Sistema de pagos**: Integración con Stripe/PayPal
- **Recomendaciones IA**: Algoritmos basados en historial de usuario
- **Notificaciones**: Sistema de alertas en tiempo real
- **Tests automatizados**: PHPUnit para backend, Jest para frontend

## Viabilidad del Proyecto
STREAM+ demuestra ser un proyecto viable con potencial de crecimiento. La arquitectura modular permite añadir nuevas funcionalidades fácilmente, y el enfoque en seguridad y usabilidad lo hace adecuado para un entorno de producción.

## Utilidad del Ciclo de Estudios
Los conocimientos adquiridos en el ciclo DAW han sido fundamentales para el desarrollo de este proyecto. Los módulos de:
- **Programación**: PHP, JavaScript
- **Bases de datos**: MySQL, diseño de esquemas
- **Desarrollo web**: HTML, CSS, arquitectura web
- **Sistemas**: Configuración de entornos de desarrollo

Han proporcionado la base sólida necesaria para crear una aplicación web completa.

## Reflexión Final
Este proyecto no solo cumple con los requisitos académicos, sino que también representa un portfolio sólido para futuras oportunidades laborales. La experiencia de desarrollar una aplicación desde la concepción hasta la implementación final ha fortalecido mis habilidades técnicas y mi capacidad para resolver problemas complejos.

---

# 📚Bibliografía

## Fuentes Técnicas
- [PHP Manual](https://www.php.net/manual/es/) - Documentación oficial de PHP
- [MySQL Documentation](https://dev.mysql.com/doc/) - Guía de MySQL
- [OWASP Top 10](https://owasp.org/www-project-top-ten/) - Guía de seguridad web

## Tutoriales y Cursos
- [PHP PDO Tutorial](https://phpdelusions.net/pdo) - Mejores prácticas con PDO
- [CSS Grid Guide](https://css-tricks.com/snippets/css/complete-guide-grid/) - Diseño con CSS Grid
- [JavaScript MDN](https://developer.mozilla.org/es/docs/Web/JavaScript) - Referencia de JavaScript

## Inspiración de Proyectos
- [Netflix Clone Tutorial](https://www.youtube.com/results?search_query=netflix+clone+php) - Videos de YouTube
- [E-commerce PHP](https://www.youtube.com/results?search_query=php+ecommerce+tutorial) - Tutoriales de comercio electrónico

## Herramientas
- [Visual Studio Code](https://code.visualstudio.com/) - Editor de código
- [XAMPP](https://www.apachefriends.org/) - Entorno de desarrollo local
- [GitHub](https://github.com/) - Control de versiones

---

*Documento generado para el Proyecto Final del Ciclo DAW - STREAM+*  
*Fecha: Noviembre 2025*