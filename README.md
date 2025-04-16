# Gestión de Asistencia y Guardias del Profesorado

Aplicación desarrollada como proyecto final del ciclo DAW, con el objetivo de facilitar el control de asistencia del profesorado y la gestión de guardias en caso de ausencias.

## 🧩 Funcionalidades principales

- **Login seguro** con autenticación (DNI y contraseña).
- **Inicio y fin de jornada laboral** con registro de fecha y hora.
- **Consulta de asistencia** por docente, día o mes (admin).
- **Registro de ausencias** del profesorado (admin).
- **Generación de informes** de ausencias por periodos (admin).
- **Consulta de profesorado ausente** para asignación de guardias.
- **Registro de guardias realizadas** por parte del profesorado.

## ⚙️ Arquitectura

La aplicación sigue una arquitectura **RESTful**, permitiendo el acceso desde dispositivos de escritorio o móviles.

## 🗃️ Base de datos

- Tabla del profesorado del centro.
- Tabla de horario lectivo del centro (guardias.sql).
- Tabla de sesiones lectivas con docentes, materias y aulas.
- Tabla de registros de entrada y salida.
- Tabla de ausencias y de guardias realizadas.

## 🔐 Registro de sesiones

Cada inicio y cierre de sesión se almacena en un archivo de texto con:
- Usuario
- Fecha
- Hora

## 🧭 Módulos de la aplicación

1. **Login**
2. **Menú principal**
3. **Consulta de asistencia** *(solo admin)*
4. **Registro de profesorado ausente** *(solo admin)*
5. **Informes de ausencias** *(solo admin)*
6. **Consulta de profesorado ausente**
7. **Consulta de guardias realizadas**

## 👨‍🏫 Lógica de guardias

- Si hay codocencia (dos docentes en un grupo), solo se muestra la sesión si **ambos** están ausentes.
- El docente de guardia puede ver las ausencias activas y asignarse a una guardia.

## 📁 Requisitos

- Servidor compatible con PHP.
- Base de datos MySQL con estructura proporcionada en `guardias.sql`.

## 📌 Nota

Este proyecto se ajusta a los criterios definidos en el módulo de proyecto del ciclo DAW.
