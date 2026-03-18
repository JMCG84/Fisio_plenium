# Fisio Plenium - Proyecto Final de Grado Superior

Este proyecto es el resultado de la migracion y modernizacion de una aplicacion de gestion para una clinica de fisioterapia. Se trata del proyecto final de mi Grado Superior, el cual ha sido transformado desde una arquitectura base en PHP clasico hacia una aplicacion moderna utilizando Vue 3 y una API desacoplada.

Esta evolucion se ha llevado a cabo con el apoyo de herramientas de Inteligencia Artificial, aplicando los conocimientos y tecnicas adquiridas en el curso de desarrollo con IA para optimizar el flujo de trabajo, mejorar la calidad del codigo y acelerar la transicion hacia tecnologias de vanguardia.

## Estructura del Proyecto

El repositorio esta organizado de la siguiente manera para separar claramente las responsabilidades del sistema:

1. Directorio Raiz: Contiene los archivos legacy en PHP y la logica de servidor que sirve de base al sistema.
2. Directorio api/: Aloja los endpoints RESTful desarrollados en PHP que gestionan la comunicacion entre el frontend y la base de datos MySQL.
3. Directorio config/: Contiene los archivos de configuracion de la base de datos y constantes del sistema.
4. Directorio frontend/: Aplicacion Single Page Application (SPA) desarrollada con Vue 3, TypeScript y Pinia.
5. Archivo fisio_plenium.sql: Script de creacion de la base de datos que incluye la estructura de tablas y los datos de inicializacion necesarios.

## Tecnologias Utilizadas

### Frontend (Vue 3 Ecosystem)

- Vue 3 (Composition API): Framework principal para la reactividad de la interfaz.
- TypeScript: Implementacion de tipado estricto para modelos de datos.
- Pinia: Gestion de estado global para el carrito de servicios y sesiones de usuario.
- Vue Router: Control de navegacion y proteccion de rutas mediante Guards.
- Bootstrap 5: Base de estilos con personalizacion de diseño moderno.

### Backend y Persistencia

- PHP 8: Desarrollo de la API para la gestion de servicios y reservas.
- MySQL: Motor de base de datos relacional.
- PDO: Acceso seguro a datos para prevenir vulnerabilidades de seguridad.

## Versiones del Proyecto y Acceso

Este proyecto permite visualizar la evolución desde una arquitectura tradicional hacia una moderna. Para ver las distintas versiones, sigue estas instrucciones:

### 1. Versión Original (Legacy - PHP/JS)
Es la versión clásica servida directamente por Apache (XAMPP).
- **Acceso:** [http://localhost/fisio_Plenium/](http://localhost/fisio_Plenium/) (Suponiendo que la carpeta está en `htdocs`).
- **Características:** Renderizado en servidor con PHP, lógica de carrito en JavaScript Vanilla.

### 2. Versión Moderna (Vue 3 + Vite)
Es la versión rediseñada con componentes reactivos y estética premium.
- **Acceso:** [http://localhost:5173/](http://localhost:5173/)
- **Instrucciones para iniciar:**
  1. Abre una terminal en la carpeta `/frontend`.
  2. Ejecuta `npm install` (solo la primera vez).
  3. Ejecuta `npm run dev`.
- **Características:** SPA (Single Page Application), TypeScript, Pinia para el estado global y diseño visual avanzado.

> [!NOTE]
> Ambas versiones comparten la misma base de datos MySQL y la misma lógica de negocio a través de la API situada en `/api`.

### Acceso al Panel de Administración

Para probar las funcionalidades de gestión, puedes usar estas credenciales:
- **Usuario:** `admin@plenium.com`
- **Contraseña:** `admin123`

---

## Instrucciones de Instalación

1. **Requisitos:** Servidor local (XAMPP o similar) con PHP y MySQL, además de Node.js instalado.
2. **Configuración de Base de Datos:** Importar el archivo `fisio_plenium.sql` en un servidor MySQL.
3. **Despliegue del Backend:** Alojar el contenido de la raíz en el directorio público del servidor (ej. `htdocs/fisio_Plenium/`).
4. **Ejecución del Frontend (Vue):**
   - Acceder a la carpeta `frontend/`.
   - Ejecutar `npm install` para las dependencias.
   - Ejecutar `npm run dev` para iniciar el servidor de desarrollo.
5. **Acceso:** Navegar a la dirección proporcionada por Vite (por defecto `http://localhost:5173`).

---
Este proyecto demuestra la capacidad de integrar métodos tradicionales de desarrollo con nuevas técnicas asistidas por IA para lograr productos de software más robustos y profesionales.
