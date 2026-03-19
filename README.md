# Fisio Plenium - Proyecto Final de Grado Superior

https://github.com/user-attachments/assets/d8826a5c-cc08-4771-9f70-43be47f43dba

Este proyecto es el resultado de la migración y modernización de una aplicación de gestión para una clínica de fisioterapia. Se trata del proyecto final de mi Grado Superior, el cual ha sido transformado desde una arquitectura base en PHP clásico hacia una aplicación moderna utilizando Vue 3 y una API desacoplada.

Esta evolución se ha llevado a cabo con el apoyo de herramientas de Inteligencia Artificial, aplicando los conocimientos y técnicas adquiridas en el curso de desarrollo con IA para optimizar el flujo de trabajo, mejorar la calidad del código y acelerar la transición hacia tecnologías de vanguardia.

## Estructura del Proyecto

El repositorio está organizado de la siguiente manera para separar claramente las responsabilidades del sistema:

1. Directorio Raíz: Contiene los archivos legacy en PHP y la lógica de servidor que sirve de base al sistema.
2. Directorio api/: Aloja los endpoints RESTful desarrollados en PHP que gestionan la comunicación entre el frontend y la base de datos MySQL.
3. Directorio config/: Contiene los archivos de configuración de la base de datos y constantes del sistema.
4. Directorio frontend/: Aplicación Single Page Application (SPA) desarrollada con Vue 3, TypeScript y Pinia.
5. Archivo fisio_plenium.sql: Script de creación de la base de datos que incluye la estructura de tablas y los datos de inicialización necesarios.

## Tecnologías Utilizadas

### Frontend (Vue 3 Ecosystem)

- Vue 3 (Composition API): Framework principal para la reactividad de la interfaz.
- TypeScript: Implementación de tipado estricto para modelos de datos.
- Pinia: Gestión de estado global para el carrito de servic
