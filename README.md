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

## Acceso al Panel de Administracion

Para realizar pruebas en el panel de gestion de reservas, se han habilitado las siguientes credenciales de administrador por defecto:

- Usuario: admin@plenium.com
- Contrasena: admin123

## Instrucciones de Instalacion

1. Requisitos: Servidor local (XAMPP o similar) con PHP y MySQL, ademas de Node.js instalado.
2. Configuracion de Base de Datos: Importar el archivo fisio_plenium.sql en un servidor MySQL.
3. Despliegue del Backend: Alojar el contenido de la raiz en el directorio publico del servidor (htdocs).
4. Ejecucion del Frontend:
   - Acceder a la carpeta frontend/.
   - Ejecutar npm install para las dependencias.
   - Ejecutar npm run dev para iniciar el servidor de desarrollo.
5. Acceso: Navegar a la direccion local proporcionada por el servidor de desarrollo (por defecto http://localhost:5173).

Este proyecto demuestra la capacidad de integrar metodos tradicionales de desarrollo con nuevas tecnicas asistidas por IA para lograr productos de software mas robustos y profesionales.
