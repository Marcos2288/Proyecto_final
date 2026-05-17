# 🌸 Portada

**STREAM+ — Plataforma de Streaming Integral**

**Autor:** [Tu Nombre]
**Curso Escolar:** 2025-2026
**Ciclo Formativo:** Desarrollo de Aplicaciones Web (DAW)

---

# 📌 Índice

1. [Introducción](#introducción)
2. [Planificación](#planificación)
3. [Estado del Arte / Investigación](#estado-del-arte--investigación)
4. [Diseño](#diseño)
5. [Implementación](#implementación)
6. [Pruebas](#pruebas)
7. [Conclusiones](#conclusiones)
8. [Bibliografía](#bibliografía)
9. [Defensa del Proyecto](#defensa-del-proyecto)

---

# ⚡ Introducción

## Tema del Proyecto
STREAM+ es una plataforma web que integra catálogo de películas y series, tienda online de merchandising y un foro de comunidad. El proyecto se enfoca en ofrecer una experiencia completa de entretenimiento, desde la exploración de contenido hasta la valoración, compra y participación social.

## Relevancia
En el contexto actual, los usuarios esperan plataformas multimedia más completas que no solo ofrezcan contenido sino también interacción social y comercio conectado. STREAM+ responde a esta necesidad mostrando cómo una aplicación puede integrar aprendizaje de desarrollo web con características prácticas y reales.

## Contexto del Problema
Las plataformas convencionales separan la experiencia de entretenimiento de la experiencia de compra. Además, los proyectos de streaming educativos suelen limitarse al catálogo y la reproducción. STREAM+ mejora esta propuesta al unir:
- Catálogo de contenido audiovisual
- Registro de visualizaciones y progreso
- Valoraciones reales de usuarios
- Tienda con dependencia de contenido visto
- Foro comunitario

## Justificación de la Elección del Tema
El proyecto se adapta a los objetivos del ciclo DAW ya que permite trabajar con:
- PHP y MySQL
- HTML/CSS y JavaScript
- Control de usuarios y sesiones
- Gestión de datos y relaciones de entidades
- Seguridad y usabilidad

## Objetivos
1. Crear una plataforma de streaming funcional.
2. Desarrollar un sistema de usuarios seguro.
3. Implementar seguimiento de contenido visto y progreso.
4. Añadir una tienda interactiva con stock y compras.
5. Incorporar valoraciones de usuario en contenido y productos.
6. Desplegar un foro para discusión y participación.
7. Documentar y justificar el proceso de desarrollo.

---

# 🗓️ Planificación

## Cronograma del Proyecto

| Fase | Duración Estimada | Duración Real | Actividades |
|------|-------------------|---------------|-------------|
| Planificación | 1 semana | 1 semana | Análisis de requisitos, definición de objetivos |
| Investigación | 5 días | 5 días | Estudio de tecnología y soluciones existentes |
| Diseño conceptual | 4 días | 4 días | Estructura de base de datos, wireframes, arquitectura |
| Implementación backend | 3 semanas | 3 semanas | Modelos, controladores, lógica de negocio |
| Implementación frontend | 2 semanas | 2 semanas | Vistas, estilo, interacción, accesibilidad |
| Integración y pruebas | 1 semana | 1 semana | Pruebas funcionales, correcciones, mejoras |
| Documentación | 1 semana | 1 semana | Redacción del informe, diagramas, capturas |

## Recursos Necesarios

### Materiales
- Ordenador con Windows
- Servidor local XAMPP instalado
- Conexión a internet para investigación

### Software
- Visual Studio Code
- PHP 7.4+ / 8.x
- MySQL / MariaDB
- Navegador web (Chrome, Firefox)
- Git (opcional)

### Recursos Humanos
- Estudiante desarrollador (yo)
- Tutor de ciclo para asesoría

## Estimación de Costes

### Tiempo
- 240 horas de trabajo estimadas

### Valoración económica (estimada)
- 15 €/hora × 240 horas = 3.600 €

### Costes adicionales
- Hosting estimado: 50 €/mes
- Dominio estimado: 10 €/año
- Total aproximado: 3.660 € + tiempo de desarrollo

---

# 🔎 Estado del Arte / Investigación

## Plataformas similares existentes

### Netflix
- Fortalezas: interfaz pulida, recomendaciones inteligentes, catálogo amplio.
- Debilidades: no integra tienda ni foro nativo para comunidad de fans.

### HBO Max
- Fortalezas: contenido exclusivo y calidad cinematográfica.
- Debilidades: experiencia de usuario menos centrada en comunidad.

### Disney+
- Fortalezas: contenido familiar y franquicias reconocidas.
- Debilidades: menor diversificación fuera de su propio universo.

### Etsy / Redbubble
- Fortalezas: comercio electrónico de merchandising con productos temáticos.
- Debilidades: no están integradas en un ecosistema audiovisual de streaming.

## Referentes de diseño y funcionalidad
- Sistemas de valoración por usuarios: ofrecen confianza y responsabilidad.
- Carritos de compra con stock: necesarios para evitar ventas de inventario inexistente.
- Foros moderados: fomentan la conversación sin dispersar la comunidad.

## Tecnologías investigadas

### Backend
- **PHP**: seleccionado por su compatibilidad con XAMPP y su uso clásico en DAW.
- **Frameworks**: se consideraron Laravel y Symfony, pero se descartaron por complejidad y alcance educativo.
- **Alternativas**: Node.js/Express, Python/Django; buenas alternativas, pero no necesarias para este proyecto.

### Base de Datos
- **MySQL**: elegido por su integración nativa con PHP y su importancia en el ciclo.
- **PostgreSQL**: poderoso, pero no requerido para el alcance actual.
- **SQLite**: no se utilizó porque el proyecto requiere varias tablas y relaciones complejas.

### Frontend
- **HTML + CSS + JavaScript vanilla**: suficiente para construir una interfaz clara y responsiva.
- **Frameworks**: React, Vue o Angular no se emplearon para mantener la curva de aprendizaje accesible.

### Seguridad
- Uso de **prepared statements** para evitar inyección SQL.
- Manejo correcto de **sesiones** y roles de usuario.
- Validación de entradas en servidor y cliente.

---

# ✍️ Diseño

## Arquitectura General
STREAM+ utiliza una arquitectura básica tipo MVC:

- **Modelos**: se encargan de la lógica de acceso a datos (`models/`).
- **Controladores**: reciben peticiones, validan datos, ejecutan operaciones (`controllers/`).
- **Vistas**: muestran contenido HTML y permiten interacción con el usuario (`views/`).

## Diagrama de Base de Datos

```
usuarios
  ├─ id
  ├─ nombre
  ├─ email
  ├─ password
  ├─ desarrollador
  └─ modo_anti_spoiler

contenidos
  ├─ id
  ├─ titulo
  ├─ tipo
  ├─ imagen
  ├─ descripcion
  └─ ...

episodios
  ├─ id
  ├─ serie_id
  ├─ temporada
  ├─ numero
  ├─ titulo
  ├─ imagen
  └─ descripcion

productos
  ├─ id
  ├─ nombre
  ├─ precio
  ├─ imagen
  ├─ categoria
  ├─ descripcion
  ├─ detalles
  ├─ stock
  ├─ contenido_id
  └─ ...

historial
  ├─ usuario_id
  ├─ contenido_id
  ├─ visto
  └─ fecha_visto

episodios_vistos
  ├─ usuario_id
  ├─ episodio_id
  ├─ visto
  └─ fecha_visto

compras
  ├─ id
  ├─ usuario_id
  ├─ producto_id
  ├─ cantidad
  ├─ precio_unitario
  └─ fecha

ratings_contenidos
  ├─ id
  ├─ usuario_id
  ├─ contenido_id
  ├─ rating
  └─ fecha

ratings_productos
  ├─ id
  ├─ usuario_id
  ├─ producto_id
  ├─ rating
  └─ fecha
```

## Diseño de Interfaces

### Páginas principales
- **Inicio**: resumen de novedades, recomendaciones y acceso rápido.
- **Cine**: catálogo de películas y series con tarjetas visuales.
- **Detalle de contenido**: información, valoración y opciones de seguimiento.
- **Tienda**: productos destacados y acceso a detalles.
- **Detalle de producto**: valoración, compra y relaciones con contenido.
- **Carrito**: resumen de compra y checkout.
- **Foro**: creación y lectura de temas, respuestas.
- **Perfil**: historial de visualización, compras y preferencias.

### Componentes visuales
- **Cartas (cards)** para contenido y productos.
- **Botones** con estilo consistente y accesible.
- **Pestañas** en perfil para organizar información.
- **Formularios** claros con validaciones visibles.

### Paleta de color
- Fondo oscuro para enfoque cinematográfico.
- Texto claro para contraste y legibilidad.
- Color acento magenta/morado para acciones principales.

## Wireframes y capturas

![Catálogo de contenidos](docs/screenshot_catalogo.svg)

_Descripción_: pantalla de catálogo con tarjetas de contenido, filtros y acceso al detalle.

![Detalle de producto](docs/screenshot_producto.svg)

_Descripción_: pantalla de producto con información, valoraciones y formulario de compra.

![Sección de foro](docs/screenshot_forum.svg)

_Descripción_: vista de foro con temas y respuestas para la comunidad.

## Flujos de usuario

### Flujo de usuario registrado
1. Login / registro.
2. Explorar contenido.
3. Marcar como visto y ver progreso.
4. Acceder a la tienda y comprar productos.
5. Valorar contenido y productos después de la compra.
6. Participar en el foro.

### Flujo de desarrollador
1. Acceso a panel de desarrollador.
2. Añadir contenido, episodios y productos.
3. No puede valorar productos desde el panel, solo usuarios compradores pueden hacerlo.

---

# 🛠️ Implementación

## Estructura del proyecto

- `config/`: configuración de base de datos y helpers.
- `controllers/`: lógica de las acciones del usuario.
- `models/`: consultas organizadas y relaciones.
- `views/`: páginas y plantillas de interfaz.
- `public/`: estilos CSS y activos.

## Funcionalidades clave

### Autenticación y roles
- Registro y login con validación.
- Sesiones de usuario.
- Distinción entre usuario normal y desarrollador.

### Gestión de contenido
- Catálogo de películas y series.
- Página de detalle con episodios para series.
- Seguimiento de visualización y completado.

### Tienda y compras
- Productos con stock y relación a contenidos.
- Carrito persistente en sesión.
- Checkout con creación de pedido y detalle.
- Reducción de stock por cantidad comprada.
- Registro de compra real en tabla `compras`.

### Valoraciones
- Sistema de rating de 1 a 10 con hasta 2 decimales.
- Usuarios solo pueden valorar contenido si lo han visto.
- Usuarios solo pueden valorar producto si lo han comprado.
- Promedio calculado dinámicamente y mostrado en tarjetas y detalle.

### Foro comunitario
- Creación de temas y respuestas.
- Interfaz de discusión básica.
- Vinculación de usuarios con publicaciones.

### Modo Anti-Spoiler
- Si está activo, la tienda oculta productos de contenido no visto.
- La tienda solo muestra mercancía vinculada a contenido que el usuario ya conoce.

## Implementación técnica detallada

### Controladores
- `ShopController.php`: maneja carrito, checkout, cupones y valoraciones.
- `EpisodeController.php`: maneja marcado de contenido visto y valoración de contenidos.
- `ContentController.php`: gestiona creación de contenido, episodios y productos en el panel de desarrollador.

### Helpers y tablas adicionales
- `ensure_ratings_tables()`: crea tablas de valoraciones y compras si no existen.
- `has_user_purchased_product()`: verifica que el usuario haya comprado el producto.
- `get_user_product_rating()` y `get_user_content_rating()`: retornan la valoración previa.

### Ajustes de stock
- Al finalizar el checkout, el stock del producto se descuenta según la cantidad comprada.
- Se valida el stock disponible antes de completar el pedido.
- El proceso usa transacción para evitar errores parciales.

### Ajustes de diseño
- Formularios de valoración con input numérico del 1 al 10.
- Eliminado campo de rating en el panel de desarrollador para productos.
- Promedio de valoración mostrado con formato `x.x/10` y cantidad de votos.

## Cambios relevantes realizados durante el desarrollo

### Cambio 1: valoración 1-10
- Inicialmente el sistema usaba puntuación 1-5.
- Se cambió a 1-10 con decimales para mayor precisión.
- Las tablas `ratings_contenidos` y `ratings_productos` ahora usan `DECIMAL(3,2)`.

### Cambio 2: stock por cantidad
- El proyecto necesitaba decrementar stock según la cantidad comprada.
- Se implementó en el checkout con `UPDATE productos SET stock = stock - ?`.

### Cambio 3: validación de compra antes de valoración
- Los productos solo podían valorarse tras compra.
- Se añadió la tabla `compras` y la comprobación `has_user_purchased_product()`.

### Cambio 4: mantenimiento de la tabla `compras`
- Se corrigió un problema en el helper que borraba `compras` al crear tablas.
- La función `ensure_ratings_tables()` ahora usa `CREATE TABLE IF NOT EXISTS`, preservando datos.

## Problemas encontrados y soluciones

### Problema: fallo al obtener rating de producto
- Se producía un error por función indefinida.
- Solución: se añadió `get_user_product_rating()` y se verificó la carga de helpers.

### Problema: las compras no se reconocían correctamente
- El carrito añadía el producto, pero no se registraba el pedido.
- Solución: se migró el registro real de compra al checkout.

### Problema: pérdida de stock
- El stock no se actualizaba en función de la cantidad comprada.
- Solución: se restó la cantidad exacta durante la operación de pago.

---

# 🌀 Pruebas

## Tipos de pruebas realizadas
- **Pruebas funcionales**: comprobar que cada botón y enlace funciona.
- **Pruebas de usuario**: registro, login, compra, valoración.
- **Pruebas de integración**: verificar interacción entre carrito, checkout y stock.
- **Pruebas de seguridad básica**: validación de inputs y prepared statements.

## Casos de prueba principales

### Registro y login
- Crear usuario nuevo.
- Intentar login con credenciales incorrectas.
- Verificar acceso restringido a vistas privadas.

### Contenido y progreso
- Marcar película como vista.
- Marcar serie completa como vista y comprobar progreso.
- Verificar que el modo anti-spoiler oculta productos no vistos.

### Carrito y checkout
- Añadir producto al carrito.
- Ajustar cantidad y ver subtotales.
- Finalizar compra y comprobar que stock disminuye.
- Verificar que el carrito se vacía después del checkout.

### Valoraciones
- Valorar contenido visto.
- Valorar producto comprado.
- Intentar valorar producto no comprado y verificar que no aparece el formulario.
- Verificar cálculo de promedio y número de calificaciones.

## Resultados de las pruebas
- Todas las funciones principales operan correctamente.
- El stock se decrementa según la cantidad comprada.
- Las valoraciones solo están disponibles cuando el usuario cumple las condiciones requeridas.
- Las transacciones de compra evitan estados inconsistentes.

---

# 🤔 Conclusiones

## Aprendizajes
- He reforzado conocimientos de PHP y MySQL al implementar un sistema completo.
- He trabajado con transacciones, validaciones y manejo de tablas relacionadas.
- He comprendido mejor el flujo entre frontend y backend en una aplicación real.

## Mejores prácticas aplicadas
- Uso de `prepared statements` para evitar inyección SQL.
- Separación de lógica en controladores, modelos y vistas.
- Validación de entradas tanto en cliente como en servidor.
- Documentación continua del proyecto.

## Mejoras futuras
- Integrar un sistema de recomendaciones basado en historial.
- Añadir pagos reales con pasarela Stripe o PayPal.
- Mejorar el foro con moderación y respuesta en hilos.
- Añadir gestión de usuarios y panel administrativo avanzado.

## Valoración del proyecto
STREAM+ cumple los objetivos planteados y demuestra la aplicación práctica de los contenidos del ciclo DAW. Es una base sólida para ampliar a un proyecto más ambicioso en el futuro.

---

# 📚 Bibliografía

- Documentación oficial de PHP: https://www.php.net/
- Manual de MySQL: https://dev.mysql.com/doc/
- OWASP Top Ten: https://owasp.org/www-project-top-ten/
- Tutoriales de HTML/CSS en MDN Web Docs: https://developer.mozilla.org/
- Recursos de desarrollo web en YouTube y foros educativos.

---

# 🧑‍🏫 Defensa del Proyecto

## Diapositivas recomendadas
- Portada con título y autor.
- Índice con los apartados clave.
- Introducción y objetivos.
- Diseño y arquitectura.
- Implementación y funcionalidades destacadas.
- Pruebas y resultados.
- Conclusiones y mejoras.

## Consejos para exponer
- Habla con claridad y sin leer literalmente.
- Usa las diapositivas solo como guía.
- Señala las partes importantes de la pantalla.
- Practica varias veces antes de la presentación.

## Demostración en directo
- Muestra el flujo completo: login, catálogo, carrito, checkout, valoración y foro.
- Ten preparado un vídeo grabado por si hay fallos técnicos.
- Controla el tiempo para no pasarte.

## Preguntas frecuentes
- ¿Qué tecnologías has usado y por qué?
- ¿Cómo manejas la seguridad de los datos?
- ¿Qué se podría mejorar en el futuro?

---

*Este documento describe el desarrollo completo del proyecto STREAM+ y recoge tanto la planificación como la implementación y la defensa del mismo.*
