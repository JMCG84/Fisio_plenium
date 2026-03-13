# Fisio Plenium - Modernización & Dashboard de Reservas 🏥✨

Este proyecto representa la evolución y modernización de una aplicación de gestión para una clínica de fisioterapia. Originalmente desarrollado en PHP clásico y Vanilla JS, ha sido transformado en una **Single Page Application (SPA)** moderna utilizando las últimas tecnologías del ecosistema Vue.

[![Vue](https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)](https://vuejs.org/)
[![TypeScript](https://img.shields.io/badge/TypeScript-5.x-3178C6?style=for-the-badge&logo=typescript&logoColor=white)](https://www.typescriptlang.org/)
[![Vite](https://img.shields.io/badge/Vite-Latest-646CFF?style=for-the-badge&logo=vite&logoColor=white)](https://vitejs.dev/)
[![Pinia](https://img.shields.io/badge/Pinia-Store-FFE066?style=for-the-badge&logo=vuedotjs&logoColor=black)](https://pinia.vuejs.org/)

## 🚀 El Desafío: Migración y Escalabilidad

El objetivo principal fue migrar la lógica de negocio y la interfaz de usuario a una arquitectura más robusta y mantenible, demostrando habilidades en:

1.  **Migración de Tech Stack**: De PHP/JS acoplado a una arquitectura desacoplada Frontend (Vue) + Backend (API PHP).
2.  **Tipado Estricto**: Implementación total de **TypeScript** para garantizar la integridad de los datos de servicios y reservas.
3.  **Estado Global**: Gestión del carrito de compras y autenticación mediante **Pinia**.
4.  **Diseño Premium**: Interfaz moderna con estética *glassmorphism*, tipografía Outfit y animaciones fluidas.

## 🛠️ Tecnologías Utilizadas

### Frontend
- **Vue 3 (Composition API)**: Framework principal.
- **TypeScript**: Tipado estricto para modelos de datos e interfaces.
- **Pinia**: Gestión de estado persistente (Carrito y Auth).
- **Vue Router**: Sistema de navegación con protectores de rutas (Guards).
- **Axios**: Comunicación asíncrona con la API.
- **Bootstrap 5**: Base de estilos personalizada con CSS moderno.

### Backend & DB
- **PHP 8 (API)**: Endpoints RESTful creados específicamente para el frontend.
- **MySQL**: Base de datos relacional para servicios, usuarios y pedidos.
- **PDO**: Consultas seguras y preparadas para evitar inyecciones SQL.

## ✨ Características Principales

- **Catálogo Dinámico**: Gestión de servicios de fisioterapia cargados en tiempo real desde DB.
- **Sistema de Reservas**: Carrito de servicios con persistencia en `localStorage`.
- **Panel de Administración**: 
  - Gestión completa de pedidos.
  - Cambio de estados (Confirmado/Cancelado) en tiempo real.
- **Autenticación y Seguridad**: 
  - Sistema de Login con roles.
  - Protección de rutas: Solo administradores pueden acceder al dashboard.
- **Resiliencia (Modo Demo)**: Sistema de fallback que permite mostrar el catálogo incluso si el servidor de base de datos no está disponible.

## 📦 Instalación y Uso

1. **Requisitos**: XAMPP (Apache + MySQL) y Node.js.
2. **Backend**: 
   - Clona este repo en tu carpeta `htdocs`.
   - Importa el archivo `fisio_plenium.sql` en phpMyAdmin.
3. **Frontend**:
   ```bash
   cd frontend
   npm install
   npm run dev
   ```
4. Abre `http://localhost:5173`.

---
*Desarrollado como proyecto final de Ciclo Superior DAW, modernizado para exhibición en Portfolio.*
