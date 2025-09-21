# Aplicación Laravel - Guía de Instalación y Desarrollo
---

## 1. Configuración del Sistema de Base de Datos (MySQL + DBeaver)

### Pasos para establecer la conexión

1. **Instalar MySQL** y generar una nueva base de datos para la aplicación:
   ```sql
   CREATE DATABASE doctor_app;
   ```

2. **Configurar DBeaver**: Establecer conexión con el servidor MySQL y confirmar la existencia de la base de datos `doctor_app`.

3. **Configurar variables de entorno**: Modificar el archivo `.env` con los parámetros de conexión:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=doctor_app
   DB_USERNAME=nombre_usuario
   DB_PASSWORD=clave_acceso
   ```

4. **Validar la conexión**: Ejecutar las migraciones para verificar el funcionamiento:
   ```bash
   php artisan migrate
   ```
   > Una ejecución exitosa confirma que la conexión está operativa.

---

## 2. Desarrollo de Layout Personalizado

### Creación del diseño base

1. **Generar archivo de plantilla** en la siguiente ubicación:
   ```
   resources/views/layouts/app.blade.php
   ```

2. **Implementar estructura fundamental**:
   ```blade
   <!DOCTYPE html>
   <html lang="es">
   <head>
       <meta charset="UTF-8">
       <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <title>{{ $title ?? 'Doctor App' }}</title>
       @vite('resources/css/app.css')
       @vite('resources/js/app.js')
   </head>
   <body class="bg-gray-100">
       <!-- Encabezado -->
       @include('partials.header')
       
       <!-- Área de contenido principal -->
       <main class="container mx-auto p-6">
           {{ $slot }}
       </main>
       
       <!-- Pie de página -->
       @include('partials.footer')
   </body>
   </html>
   ```

3. **Organizar componentes**: Establecer la carpeta `partials` e incluir elementos como `header.blade.php` y `footer.blade.php`.

---

## 3. Incorporación de Flowbite

### Proceso de integración

1. **Configurar TailwindCSS** (si no está instalado previamente):
   ```bash
   npm install -D tailwindcss postcss autoprefixer
   npx tailwindcss init -p
   ```

2. **Ajustar configuración** en el archivo `tailwind.config.js`:
   ```js
   export default {
     content: [
       "./resources/**/*.blade.php",
       "./resources/**/*.js",
       "./resources/**/*.vue",
       "./node_modules/flowbite/**/*.js"
     ],
     theme: {
       extend: {},
     },
     plugins: [
       require('flowbite/plugin')
     ],
   }
   ```

3. **Añadir Flowbite** al proyecto:
   ```bash
   npm install flowbite
   ```

4. **Incorporar en JavaScript**: Modificar el archivo `app.js`:
   ```js
   import 'flowbite';
   ```

5. **Compilar recursos**:
   ```bash
   npm run dev
   ```

---

## 4. Implementación de Slots e Includes

### Ejemplos prácticos

**Componente reutilizable con slot**  
Ubicación: `resources/views/components/card.blade.php`
```blade
<div class="p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-lg font-bold">{{ $title }}</h2>
    <div>
        {{ $slot }}
    </div>
</div>
```

**Implementación en vista**:
```blade
<x-card title="Página de Inicio">
    <p>Contenido dinámico insertado mediante slot.</p>
</x-card>
```

**Utilización de includes**:
```blade
@include('partials.header')
```

> La correcta visualización indica que los sistemas de `slots` e `includes` están operativos.

---

## Lista de Verificación del Sistema

- [x] Conectividad MySQL validada mediante DBeaver
- [x] Plantilla principal establecida en `resources/views/layouts/app.blade.php`
- [x] Flowbite correctamente integrado con Tailwind y NPM
- [x] Funcionalidad de Slots e Includes verificada

---

## Comandos Esenciales

```bash
php artisan serve       # Iniciar servidor de desarrollo
php artisan migrate     # Aplicar migraciones de base de datos
npm run dev             # Compilar recursos (Tailwind + Flowbite)
```
