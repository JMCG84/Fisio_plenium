# Fisio Plenium - Modernización y Dashboard de Reservas

Este proyecto representa la evolución y modernización de una aplicación de gestión para una clínica de fisioterapia. Originalmente desarrollado en PHP clásico y Vanilla JS, ha sido transformado en una Single Page Application (SPA) moderna utilizando el ecosistema Vue 3.

## El Desafío: Migración y Escalabilidad

El objetivo principal fue migrar la lógica de negocio y la interfaz de usuario a una arquitectura más robusta y mantenible, demostrando competencias en:

1. Migración de Tech Stack: Transición de una arquitectura acoplada en PHP a una estructura desacoplada Frontend (Vue) y Backend (API PHP).
2. Tipado Estricto: Implementación de TypeScript para garantizar la integridad de los modelos de datos.
3. gestión de Estado: Uso de Pinia para la persistencia del carrito de compras y la sesión de usuario.
4. Diseño Profesional: Interfaz moderna con estética glassmorphism, tipografía Outfit y transiciones fluidas.

## Tecnologías Utilizadas

### Frontend
- Vue 3 (Composition API): Framework principal para la construcción de la interfaz.
- TypeScript: Tipado estricto para modelos de datos e interfaces de servicios.
- Pinia: Gestión de estado global y persistente.
- Vue Router: Sistema de navegación con protectores de rutas (Guards).
- Axios: Cliente HTTP para la comunicación con la API.
- Bootstrap 5: Base de estilos personalizada.

### Backend y Base de Datos
- PHP 8 (API): Endpoints RESTful desarrollados para la interacción con el frontend.
- MySQL: Almacenamiento relacional de servicios, usuarios y reservas.
- PDO: Gestión segura de consultas para prevenir inyecciones SQL.

## Características Principales

- Catálogo Dinámico: Carga asíncrona de servicios de fisioterapia desde la base de datos.
- Sistema de Reservas: Gestión de carrito de servicios con persistencia en localStorage.
- Panel de Administración: Dashboard para la gestión de pedidos y actualización de estados en tiempo real.
- Autenticación y Seguridad: Sistema de login centralizado con protección de rutas para perfiles administrativos.
- Resiliencia del Sistema: Mecanismo de fallback para garantizar el funcionamiento del catálogo en caso de indisponibilidad del servidor.

## Instalación y Configuración

1. Requisitos: Entorno PHP (Apache + MySQL) y Node.js.
2. Configuración del Backend:
   - Alojar el proyecto en el directorio del servidor web.
   - Importar el esquema de base de datos contenido en fisio_plenium.sql.
3. Configuración del Frontend:
   - Acceder al directorio frontend.
   - Ejecutar npm install para instalar dependencias.
   - Ejecutar npm run dev para iniciar el servidor de desarrollo.
4. Acceso: La aplicación estará disponible por defecto en http://localhost:5173.

## Configuración de Datos Iniciales

Para que el sistema de login y catálogo funcione, asegúrate de ejecutar estas sentencias SQL en tu base de datos:

```sql
-- Insertar un administrador por defecto (password: admin)
INSERT INTO usuarios (nombre, email, password_hash, rol) 
VALUES ('Administrador', 'admin@fisio.com', 'admin', 'admin');

-- Insertar servicios iniciales
INSERT INTO servicios (nombre, descripcion, precio, activo) VALUES 
('Fisioterapia deportiva', 'Optimiza tu rendimiento.', 45.00, 1),
('Fisioterapia traumatológica', 'Lesiones óseas y articulares.', 50.00, 1),
('Fisioterapia pediátrica', 'Desarrollo motor infantil.', 48.00, 1),
('Fisioterapia respiratoria', 'Mejora función pulmonar.', 42.00, 1),
('Suelo pélvico', 'Rehabilitación postparto.', 50.00, 1),
('Osteopatía', 'Equilibrio corporal global.', 55.00, 1);
```

---
Proyecto de Ciclo Superior en Desarrollo de Aplicaciones Web, modernizado con tecnologías de vanguardia para su exhibición profesional.
