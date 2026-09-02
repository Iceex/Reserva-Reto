# Sistema de reservas

Aplicación desarrollada con **Laravel 12, PHP y SQLite**, utilizando Blade para la interfaz y Tailwind CSS para los estilos.

## Instalación
Clonar el repositorio:

```bash
git clone https://github.com/Iceex/Reserva-Reto.git reserva-tecnica
cd reserva-tecnica
```

Dependencias:

```bash
composer install
```

Crear el archivo de entorno:

```bash
copy .env.example .env
```

Generar la clave de la aplicación:

```bash
php artisan key:generate
```

Ejecutar migraciones y seeders:

```bash
php artisan migrate:fresh --seed
```

Iniciar el servidor:

```bash
php artisan serve
```

Abrir:

```text
http://127.0.0.1:8000
```


## usuario de prueba

El seeder crea un usuario para probar el sistema:

```text
Email: demo@example.com
Password: password
```

También se puede crear un usuario desde el formulario de registro.


## reto

### 1. ABM de mesas

El sistema contempla mesas distribuidas en las ubicaciones:

- A
- B
- C
- D

Cada mesa tiene ubicación, número de mesa y cantidad de personas.

Para esta implementación se generan 3 mesas por ubicación mediante el seeder.

### 2. login y registro

Se implementó autenticación mediante las herramientas nativas de Laravel y vistas Blade.

No se utiliza Sanctum porque la aplicación no requiere una API autenticada: el flujo es web y utiliza sesiones.

### 3. solicitud de reserva

El usuario autenticado puede solicitar una reserva indicando:

- fecha
- hora
- cantidad de personas

Se aplican las siguientes reglas:

- lunes a viernes: 10:00 a 24:00
- sábado: 22:00 a 02:00
- domingo: 12:00 a 16:00
- duración predeterminada: 2 horas
- mínimo 15 minutos de anticipación
- máximo 3 mesas por reserva
- las mesas utilizadas pertenecen a la misma ubicación
- la ubicación se asigna automáticamente por orden: A → B → C → D

La disponibilidad se cachea por ubicación para reducir consultas repetitivas.

El cache es una optimización; la base de datos continúa siendo la fuente de verdad para confirmar la reserva.

### 4. listado de reservas

El sistema permite consultar las reservas por fecha y muestra:

- horario
- usuario
- cantidad de personas
- ubicación
- mesas asignadas

El listado obtiene las reservas, su ubicación y las mesas asociadas mediante una única consulta SQL utilizando `JOIN` y `GROUP_CONCAT`, evitando consultas adicionales por cada reserva.

## requisitos

- PHP 8.2+
- Composer
- Laravel 12
- SQLite

No es necesario instalar MySQL ni ejecutar un servidor de base de datos.



## decisiones técnicas

### sqlite

Se utiliza SQLite para poder ejecutar la prueba localmente sin configurar un servidor MySQL.

### blade

La interfaz utiliza Blade y JavaScript nativo. No se incorporó Vue, React u otro framework frontend porque la consigna no lo requiere.

### cache

La disponibilidad se cachea por ubicación para reducir consultas repetitivas. El cache se invalida cuando una reserva modifica la disponibilidad.

### selección de mesas

El sistema intenta resolver la reserva utilizando la menor cantidad de mesas posible, respetando el máximo de 3 mesas y manteniendo todas las mesas dentro de una misma ubicación.

### listado

El listado evita el patrón N+1 y obtiene las mesas asociadas mediante una única consulta SQL.

### validación

Las reglas de negocio se validan en backend. Las validaciones del frontend solamente mejoran la experiencia del usuario.

## nota sobre concurrencia

SQLite se utiliza por simplicidad para la evaluación local. En producción utilizaría MySQL o PostgreSQL para disponer de un modelo de concurrencia y bloqueo más apropiado.

La reserva se procesa dentro de una transacción y se vuelve a comprobar la disponibilidad antes de confirmar la operación.

## estructura principal

```text
app/
├── Http/Controllers/
├── Models/
└── Services/

database/
├── migrations/
└── seeders/

resources/
└── views/

routes/
└── web.php

tests/
└── Feature/
```

## alcance

La implementación se concentra en los puntos solicitados: **reservas y listado de reservas**.

El ABM completo de mesas no forma parte del alcance solicitado, aunque las mesas necesarias para probar el flujo se generan mediante el seeder.
